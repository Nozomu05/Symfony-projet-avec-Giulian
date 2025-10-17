<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/livres')]
class LivreController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LivreRepository $livreRepository;
    private AuteurRepository $auteurRepository;
    private CategorieRepository $categorieRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        CategorieRepository $categorieRepository
    ) {
        $this->entityManager = $entityManager;
        $this->livreRepository = $livreRepository;
        $this->auteurRepository = $auteurRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('', name: 'api_livre_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['titre']) || !isset($data['auteur_nom']) || !isset($data['auteur_prenom']) || !isset($data['categorie_nom']) || !isset($data['date_publication'])) {
            return $this->json([
                'error' => 'Les champs titre, auteur_nom, auteur_prenom, categorie_nom et date_publication sont obligatoires'
            ], Response::HTTP_BAD_REQUEST);
        }

        $auteur = $this->auteurRepository->findOneBy([
            'nom' => $data['auteur_nom'],
            'prenom' => $data['auteur_prenom']
        ]);
        if (!$auteur) {
            $auteur = new \App\Entity\Auteur();
            $auteur->setNom($data['auteur_nom']);
            $auteur->setPrenom($data['auteur_prenom']);
            if (isset($data['auteur_biographie'])) {
                $auteur->setBiographie($data['auteur_biographie']);
            }
            if (isset($data['auteur_date_naissance'])) {
                $auteur->setDateNaissance(new \DateTime($data['auteur_date_naissance']));
            }
            $this->entityManager->persist($auteur);
        }

        $categorie = $this->categorieRepository->findOneBy([
            'nom' => $data['categorie_nom']
        ]);
        if (!$categorie) {
            $categorie = new \App\Entity\Categorie();
            $categorie->setNom($data['categorie_nom']);
            $categorie->setDescription($data['categorie_description'] ?? 'Aucune description');
            $this->entityManager->persist($categorie);
        }

        $livre = new Livre();
        $livre->setTitre($data['titre']);
        $livre->setAuteur($auteur);
        $livre->setCategorie($categorie);
        $livre->setDatePublication(new \DateTime($data['date_publication']));
        $livre->setDisponible($data['disponible'] ?? true);
        $this->entityManager->persist($livre);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Livre créé avec succès',
            'livre' => $this->MiseenpageLivre($livre)
        ], Response::HTTP_CREATED);
    }

    #[Route('', name: 'api_livre_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $livres = $this->livreRepository->findAll();
        $data = [];
        foreach ($livres as $livre) {
            $data[] = $this->MiseenpageLivre($livre);
        }
        return $this->json($data);
    }

    #[Route('/{id}', name: 'api_livre_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $livre = $this->livreRepository->find($id);
        if (!$livre) {
            return $this->json(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($this->MiseenpageLivre($livre));
    }

    #[Route('/{id}', name: 'api_livre_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $livre = $this->livreRepository->find($id);
        if (!$livre) {
            return $this->json(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        if (isset($data['titre'])) {
            $livre->setTitre($data['titre']);
        }
        if (isset($data['auteur_nom']) && isset($data['auteur_prenom'])) {
            $auteur = $this->auteurRepository->findOneBy([
                'nom' => $data['auteur_nom'],
                'prenom' => $data['auteur_prenom']
            ]);
            if (!$auteur) {
                $auteur = new \App\Entity\Auteur();
                $auteur->setNom($data['auteur_nom']);
                $auteur->setPrenom($data['auteur_prenom']);
                if (isset($data['auteur_biographie'])) {
                    $auteur->setBiographie($data['auteur_biographie']);
                }
                if (isset($data['auteur_date_naissance'])) {
                    $auteur->setDateNaissance(new \DateTime($data['auteur_date_naissance']));
                }
                $this->entityManager->persist($auteur);
            }
            $livre->setAuteur($auteur);
        }
        
        if (isset($data['categorie_nom'])) {
            $categorie = $this->categorieRepository->findOneBy([
                'nom' => $data['categorie_nom']
            ]);
            if (!$categorie) {
                $categorie = new \App\Entity\Categorie();
                $categorie->setNom($data['categorie_nom']);
                $categorie->setDescription($data['categorie_description'] ?? 'Aucune description');
                $this->entityManager->persist($categorie);
            }
            $livre->setCategorie($categorie);
        }
        if (isset($data['date_publication'])) {
            $livre->setDatePublication(new \DateTime($data['date_publication']));
        }
        if (isset($data['disponible'])) {
            $livre->setDisponible($data['disponible']);
        }
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Livre mis à jour avec succès',
            'livre' => $this->MiseenpageLivre($livre)
        ]);
    }

 
    #[Route('/{id}', name: 'api_livre_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $livre = $this->livreRepository->find($id);
        if (!$livre) {
            return $this->json(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($livre);
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Livre supprimé avec succès'
        ], Response::HTTP_OK);
    }

    private function MiseenpageLivre(Livre $livre): array
    {
        return [
            'id' => $livre->getId(),
            'titre' => $livre->getTitre(),
            'date_publication' => $livre->getDatePublication()?->format('Y-m-d'),
            'disponible' => $livre->isDisponible(),
            'auteur' => [
                'id' => $livre->getAuteur()->getId(),
                'nom' => $livre->getAuteur()->getNom(),
                'prenom' => $livre->getAuteur()->getPrenom()
            ],
            'categorie' => [
                'id' => $livre->getCategorie()->getId(),
                'nom' => $livre->getCategorie()->getNom()
            ]
        ];
    }
}
