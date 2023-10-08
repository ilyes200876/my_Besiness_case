<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\NftRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user')]
class ApiUserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_user_all', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'allUsers']);
        
    }

    #[Route('/show/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $user = $this->userRepository->find($id);

        return $this->json($user, 200, [], ['groups' => 'oneUser']);
    }

    
    #[Route('/add', name: 'app_add_user', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, NftRepository $nftRepository, PasswordHasherInterface $passwordHasher, AddressRepository $addressRepository): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $address = $addressRepository->find($data["address"]);

        $user = new User();
        $user->setFirstName($data["firstName"]);
        $user->setLastName($data["lastName"]);
        $user->setGender($data["gender"]);
        $user->setEmail($data["email"]);
        $user->setBirthDate(new \DateTime($data["birthDate"]));
        $user->setNickname($data["nickname"]);
        $password = $passwordHasher->hash($data["password"]);
        $user->setPassword($password);
        
        
        $user->setAddress($address);
        $nfts = [];
        for($i = 0; $i < count($data["nfts"]); $i++){
            $nfts[] = $nftRepository->findBy(["id" => $data["nfts"][$i]]);
            $user->addNft($nfts[$i][0]);
        }
        $roles = $data["roles"];
        $user->setRoles($roles);
        
        try{
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json("User added with success", 201);
        }
        catch(\Exception $e){
            return $this->json($e, 400);
        }
    }
    

    #[Route('/update/{id}', name: 'app_user_update', methods: ['UPDATE'])]
    public function update()
    {
        


    }

    #[Route('/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(User $user): JsonResponse {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->json("User deleated", 204);
    }

}
