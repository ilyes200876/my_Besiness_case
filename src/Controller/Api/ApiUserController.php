<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user')]
class ApiUserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_user_all')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'allUsers']);
        
    }

    #[Route('/show/{id}', name: 'app_user_one')]
    public function show(int $id): Response
    {
        $user = $this->userRepository->find($id);

        return $this->json($user, 200, [], ['groups' => 'oneUser']);
    }

    
    #[Route('/add/', name: 'app_user_add')]
    public function add(Request $request, SerializerInterface $serializer): JsonResponse {
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (\Exception $e) {
            return $this->json('Invalid Body', 400);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->json($user, 201);
    }
    

    #[Route('/update/{id}', name: 'app_user_update')]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_user_delete')]
    public function delete(User $user): JsonResponse {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->json("User deleated", 204);
    }

}
