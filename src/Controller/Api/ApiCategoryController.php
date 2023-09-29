<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/Category')]
class ApiCategoryController extends AbstractController
{
    
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_categogy_all')]
    public function index(): Response
    {
        
        $categories = $this->entityManager->$this->categoryRepository->getAll();

        return $this->json($categories);
        
    }

    #[Route('/show/{id}', name: 'app_categogy_show')]
    public function show(int $id): Response
    {
        $category = $this->entityManager->$this->categoryRepository->find($id);

        return $this->json($category);
    }
    
    #[Route('/add/', name: 'app_category_add')]
    public function add()
    {

    }
    

    #[Route('/update/{id}', name: 'app_category_update')]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_category_delete')]
    public function delete(Category $category): JsonResponse {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
        return $this->json("category deleated", 204);
    }

}
