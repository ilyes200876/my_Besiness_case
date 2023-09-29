<?php

namespace App\Controller\Api;

use App\Entity\SubCategory;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/subCategory')]
class ApiSubCategoryController extends AbstractController
{
    public function __construct(
        private SubCategoryRepository $subCategoryRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_subCategogy_all')]
    public function index(): Response
    {
        
        $subCategories = $this->entityManager->$this->subCategoryRepository->getAll();

        return $this->json($subCategories);
        
    }

    #[Route('/show/{id}', name: 'app_subCategogy_show')]
    public function show(int $id): Response
    {
        $subCategory = $this->entityManager->$this->subCategoryRepository->find($id);

        return $this->json($subCategory);
    }
    
    #[Route('/add/', name: 'app_subCategory_add')]
    public function add()
    {

    }
    

    #[Route('/update/{id}', name: 'app_subCategory_update')]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_subCategory_delete')]
    public function delete(SubCategory $subCategory): JsonResponse {
        $this->entityManager->remove($subCategory);
        $this->entityManager->flush();
        return $this->json("subCategory deleated", 204);
    }

}
