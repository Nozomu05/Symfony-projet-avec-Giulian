<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/utilisateurs')]
class UtilisateurController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    #[Route('', name: 'api_utilisateur_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['nom']) || !isset($data['prenom'])) {
            return $this->json([
                'error' => 'Les champs nom et prenom sont obligatoires'
            ], Response::HTTP_BAD_REQUEST);
        }
        $utilisateurExistant = $this->utilisateurRepository->findOneBy([
            'nom' => $data['nom'],
            'prenom' => $data['prenom']
        ]);
        if ($utilisateurExistant) {
            return $this->json([
                'error' => 'Un utilisateur avec ce nom et prénom existe déjà',
                'utilisateur' => $this->FormatUtilisateur($utilisateurExistant)
            ], Response::HTTP_CONFLICT);
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'utilisateur' => $this->FormatUtilisateur($utilisateur)
        ], Response::HTTP_CREATED);
    }

    #[Route('', name: 'api_utilisateur_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $utilisateurs = $this->utilisateurRepository->findAll();
        $data = [];
        foreach ($utilisateurs as $utilisateur) {
            $data[] = $this->FormatUtilisateur($utilisateur);
        }
        return $this->json($data);
    }

    #[Route('/{id}', name: 'api_utilisateur_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $utilisateur = $this->utilisateurRepository->find($id);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($this->FormatUtilisateur($utilisateur));
    }

    private function FormatUtilisateur(Utilisateur $utilisateur): array
    {
        return [
            'id' => $utilisateur->getId(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom()
        ];
    }
}
