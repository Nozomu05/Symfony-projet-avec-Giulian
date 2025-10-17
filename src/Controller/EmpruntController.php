<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/emprunts')]
class EmpruntController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EmpruntRepository $empruntRepository;
    private LivreRepository $livreRepository;
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EmpruntRepository $empruntRepository,
        LivreRepository $livreRepository,
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->entityManager = $entityManager;
        $this->empruntRepository = $empruntRepository;
        $this->livreRepository = $livreRepository;
        $this->utilisateurRepository = $utilisateurRepository;
    }
    #[Route('/emprunter', name: 'api_emprunt_create', methods: ['POST'])]
    public function emprunter(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['utilisateur_nom']) || !isset($data['utilisateur_prenom']) || !isset($data['livre_titre'])) {
            return $this->json([
                'error' => 'Les champs utilisateur_nom, utilisateur_prenom et livre_titre sont obligatoires'
            ], Response::HTTP_BAD_REQUEST);
        }

        $utilisateur = $this->utilisateurRepository->findOneBy([
            'nom' => $data['utilisateur_nom'],
            'prenom' => $data['utilisateur_prenom']
        ]);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $livre = $this->livreRepository->findOneBy([
            'titre' => $data['livre_titre']
        ]);
        if (!$livre) {
            return $this->json(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $empruntEnCours = $this->empruntRepository->findOneBy([
            'livre' => $livre,
            'dateRetour' => null
        ]);
        if ($empruntEnCours) {
            return $this->json([
                'error' => 'Ce livre est déjà emprunté par un autre utilisateur'
            ], Response::HTTP_CONFLICT);
        }
        
        $empruntsActifs = $this->empruntRepository->findBy([
            'utilisateur' => $utilisateur,
            'dateRetour' => null
        ]);
        if (count($empruntsActifs) >= 4) {
            return $this->json([
                'error' => 'L\'utilisateur a déjà emprunté 4 livres. Il doit en rendre un avant d\'emprunter un nouveau livre.'
            ], Response::HTTP_CONFLICT);
        }

        $emprunt = new Emprunt();
        $emprunt->setUtilisateur($utilisateur);
        $emprunt->setLivre($livre);
        $emprunt->setDateEmprunt(new \DateTime());
        $emprunt->setDateRetour(null);
        $livre->setDisponible(false);
        $this->entityManager->persist($emprunt);
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Livre emprunté avec succès',
            'emprunt' => $this->FormatEmprunt($emprunt)
        ], Response::HTTP_CREATED);
    }

    #[Route('/rendre/{id}', name: 'api_emprunt_retour', methods: ['PUT', 'PATCH'])]
    public function rendre(int $id): JsonResponse
    {
        $emprunt = $this->empruntRepository->find($id);
        if (!$emprunt) {
            return $this->json(['error' => 'Emprunt non trouvé'], Response::HTTP_NOT_FOUND);
        }
        if ($emprunt->getDateRetour() !== null) {
            return $this->json([
                'error' => 'Ce livre a déjà été rendu'
            ], Response::HTTP_CONFLICT);
        }
        $emprunt->setDateRetour(new \DateTime());
        $livre = $emprunt->getLivre();
        $livre->setDisponible(true);
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Livre rendu avec succès',
            'emprunt' => $this->FormatEmprunt($emprunt)
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'api_emprunt_utilisateur', methods: ['GET'])]
    public function empruntsUtilisateur(int $id): JsonResponse
    {
        $utilisateur = $this->utilisateurRepository->find($id);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $empruntsEnCours = $this->empruntRepository->findBy([
            'utilisateur' => $utilisateur,
            'dateRetour' => null
        ]);

        $data = [];
        foreach ($empruntsEnCours as $emprunt) {
            $data[] = $this->FormatEmprunt($emprunt);
        }

        return $this->json([
            'utilisateur' => [
                'id' => $utilisateur->getId(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom()
            ],
            'nombre_emprunts' => count($empruntsEnCours),
            'livres_restants' => 4 - count($empruntsEnCours),
            'emprunts' => $data
        ]);
    }

    #[Route('', name: 'api_emprunt_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $emprunts = $this->empruntRepository->findAll();
        $data = [];
        foreach ($emprunts as $emprunt) {
            $data[] = $this->FormatEmprunt($emprunt);
        }
        return $this->json($data);
    }

    #[Route('/en-cours', name: 'api_emprunt_en_cours', methods: ['GET'])]
    public function empruntsEnCours(): JsonResponse
    {
        $emprunts = $this->empruntRepository->findBy(['dateRetour' => null]);
        $data = [];
        foreach ($emprunts as $emprunt) {
            $data[] = $this->FormatEmprunt($emprunt);
        }
        return $this->json([
            'nombre_total' => count($emprunts),
            'emprunts' => $data
        ]);
    }

    private function FormatEmprunt(Emprunt $emprunt): array
    {
        return [
            'id' => $emprunt->getId(),
            'date_emprunt' => $emprunt->getDateEmprunt()?->format('Y-m-d'),
            'date_retour' => $emprunt->getDateRetour()?->format('Y-m-d'),
            'statut' => $emprunt->getDateRetour() === null ? 'En cours' : 'Rendu',
            'utilisateur' => [
                'id' => $emprunt->getUtilisateur()->getId(),
                'nom' => $emprunt->getUtilisateur()->getNom(),
                'prenom' => $emprunt->getUtilisateur()->getPrenom()
            ],
            'livre' => [
                'id' => $emprunt->getLivre()->getId(),
                'titre' => $emprunt->getLivre()->getTitre(),
                'disponible' => $emprunt->getLivre()->isDisponible()
            ]
        ];
    }
}
