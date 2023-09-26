<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/Adresse')]
class AddressController extends AbstractController
{
    public function __construct(
        private AddressRepository $addressRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_adsress_all')]
    public function index(): Response
    {
        
        $adsresses = $this->entityManager->$this->addressRepository->getAll();

        return $this->json($adsresses);
        
    }

    #[Route('/show/{id}', name: 'app_address_show')]
    public function show(int $id): Response
    {
        $address = $this->entityManager->$this->addressRepository->find($id);

        return $this->json($address);
    }
    
    #[Route('/add/', name: 'app_address_add')]
    public function add()
    {

    }
    

    #[Route('/update/{id}', name: 'app_address_update')]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_address_delete')]
    public function delete(Address $address): JsonResponse {
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        return $this->json("address deleated", 204);
    }

}
