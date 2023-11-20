<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\NftRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user')]
class ApiUserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_all_user', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'user']);
        
    }

    #[Route('/show/{id}', name: 'app_show_user', methods: ['GET'])]
    public function show(int $id): Response
    {
        $user = $this->userRepository->find($id);

        return $this->json($user, 200, [], ['groups' => 'user']);
    }

    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/add', name: 'app_add_user', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, NftRepository $nftRepository, UserPasswordHasherInterface $passwordHasher, AddressRepository $addressRepository): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $address = new Address();

        if (isset($data['country'])) {
            $address->setCountry($data['country']);
        }
        if (isset($data['department'])) {
            $address->setDepartment($data['department']);
        }
        if (isset($data['street'])) {
            $address->setStreet($data['street']);
        }
        if (isset($data['zipCode'])) {
            $address->setZipCode($data['zipCode']);
        }
        $user = new User();
        if(isset($data['firstName'])) {
            $user->setFirstName($data["firstName"]);
        }
        if(isset($data["lastName"])) {
            $user->setLastName($data["lastName"]);
        }
        if(isset($data["gender"])){
            $user->setGender($data["gender"]);
        }
        if(isset($data["email"])){
            $user->setEmail($data["email"]);
        }
        if(isset($dat['birthDate'])){
            $user->setBirthDate(new \DateTime($data["birthDate"]));
        }
        if(isset($data['nickname'])){
            $user->setNickname($data["nickname"]);
        }

        if(isset($data["password"])){
            $passwordReceived = $data["password"];
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $passwordReceived
            );
            $user->setPassword($hashedPassword);
        }
        if(isset($data["profilePic"])){
            $user->setProfilePic($data["profilePic "]);
        }
        
        $user->setAddress($address);

        if(isset($data['roles'])){
            $roles = $data["roles"];
            $user->setRoles($roles);
        }
        
        try{
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json("User added with success", 201);
        }
        catch(\Exception $e){
            return $this->json($e, 400);
        }
    }
    

    #[Route('/update/{id}', name: 'app_update_user', methods: ['PUT'])]
    public function update(int $id,Request $request, UserRepository $userRepository, TokenInterface $token )
    {
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->find($id);
        $userConnected = $token->getUser(); 


        $user->setFirstName($data["firstName"]);
        $user->setLastName($data["lastName"]);
        $user->setGender($data["gender"]);
        $user->setBirthDate(new \DateTime($data["birthDate"]));
        $user->setNickname($data["nickname"]);
        $user->setProfilePic($data["profilePic"]);
        
        $roles = $data["roles"];
        $user->setRoles($roles);

        if (!$user){
            return $this->json("User not found");
        }
        if ($user === $userConnected){
            try{
                
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->json("User updated with success", 201);
            
            }
            catch(\Exception $e){
                return $this->json($e, 400);
            }
        }else{
            return $this->json("Unauthorized", 401);
        }


    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/delete/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    public function delete(int $id, UserRepository $userRepository): JsonResponse {
        $user = $userRepository->find($id);
        if ($user){
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return $this->json("User deleated", 204);
        }else{
            return $this->json("User not found", 400);
        }
    }

}
