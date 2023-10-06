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

#[Route('/api/address')]
class ApiAddressController extends AbstractController
{
    public function __construct(
        private AddressRepository $addressRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_adsress_all', methods: ['GET'])]
    public function index(): Response
    {
        
        $address = $this->addressRepository->findAll();

        return $this->json($address, 200, [], ['groups' => 'allAddress']);
        
    }

    #[Route('/show/{id}', name: 'app_address_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $address = $this->addressRepository->find($id);

        return $this->json($address, 200, [], ['groups' => 'oneAddress']);
    }
    
    #[Route('/add', name: 'app_address_add', methods: ['POST'])]
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
    

    #[Route('/update/{id}', name: 'app_address_update', methods: ['EDIT'])]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_address_delete', methods: ['DELETE'])]
    public function delete(Address $address): JsonResponse {
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        return $this->json("address deleated", 204);
    }

}
