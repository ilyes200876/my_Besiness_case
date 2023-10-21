<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/api/address')]
class ApiAddressController extends AbstractController
{
    public function __construct(
        private AddressRepository $addressRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_all_adsress', methods: ['GET'])]
    public function index(): Response
    {
        
        $address = $this->addressRepository->findAll();

        return $this->json($address, 200, [], ['groups' => 'allAddress']);
        
    }

    #[Route('/show/{id}', name: 'app_show_address', methods: ['GET'])]
    public function show(int $id): Response
    {
        $address = $this->addressRepository->find($id);

        return $this->json($address, 200, [], ['groups' => 'oneAddress']);
    }
    
    #[Route('/add', name: 'app_add_address', methods: ['POST'])]
    public function add(Request $request, UserRepository $userRepository)
    {
        $data = json_decode($request->getContent(), true);

        $address = new Address();

        $address->setDepartment($data["department"]);
        $address->setCountry($data["country"]);
        $address->setStreet($data["street"]);
        $address->setZipCode($data["zipCode"]);
        $users =[];

        for($i = 0 ; $i<count($data["users"]); $i++){
            $users[] = $userRepository->findBy(["id" => $data["users"][$i]]);
            $address->addUser($users[$i][0]);
        }

        try{
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            return $this->json("Address Added with Success", 201);
        }
        catch(\Exception $e){
            return $this->json($e, 400);
        }

    }
    

    #[Route('/update/{id}', name: 'app_update_address', methods: ['PUT'])]
    public function update(Request $request, int $id, AddressRepository $addressRepository, TokenInterface $token)
    {
        $data= json_decode($request->getContent(), true);
        $address = $addressRepository->find($id);

        $userConnected = $token->getUser();

        $addressOwner = [];
        $addressOwner = $address->getUsers();
        // dd($addressOwner);

        $address->setCountry($data['country']);
        $address->setDepartment($data['department']);
        $address->setStreet($data['street']);
        $address->setZipCode($data['zipCode']);
        
        if(!$address){
            return $this->json("Address not found",400);
        }

        if (in_array($userConnected, $addressOwner )) {
            try {
                $this->entityManager->persist($address);
                $this->entityManager->flush();
                return $this->json("Address updated with Success", 200);
            } catch (\Exception $e) {
                return $this->json($e->getMessage(), 400);
            }
        }else{
            return $this->json("Unauthorized", 401);
        }

    }

    // #[Route('/delete/{id}', name: 'app_delete_address', methods: ['DELETE'])]
    // public function delete(Address $address): JsonResponse {
    //     $this->entityManager->remove($address);
    //     $this->entityManager->flush();
    //     return $this->json("address deleated", 204);
    // }

}
