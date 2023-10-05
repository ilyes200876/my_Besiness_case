<?php

namespace App\Controller\Api;

use App\Entity\SubCategory;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/subCategory')]
class ApiSubCategoryController extends AbstractController
{
    public function __construct(
        private SubCategoryRepository $subCategoryRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_subCategogy_all', methods: ['GET'])]
    public function index(): Response
    {
        
        $subCategories = $this->entityManager->$this->subCategoryRepository->getAll();

        return $this->json($subCategories);
        
    }

    #[Route('/show/{id}', name: 'app_subCategogy_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $subCategory = $this->entityManager->$this->subCategoryRepository->find($id);

        return $this->json($subCategory);
    }
    
    #[Route('/add', name: 'app_subCategory_add', methods: ['POST'])]
    public function add(Request $request, CategoryRepository $categoryRepository)
    {
        $data = json_decode($request->getContent(), true);

        $categoryId = $data["categoryId"];
        $category = $categoryRepository->find($categoryId);
        
        $subCategory = new SubCategory();
        
        
        $subCategory->setSubCategoryName($data['subCategoryName']);
        $subCategory->setCategory($category);

        try{
            $this->entityManager->persist($subCategory);
            $this->entityManager->flush();
            return $this->json('added with success', 201);
        }
        catch(\Exception $e){
            return $this->json($e, 400);
        }
        

    }
    

    #[Route('/update/{id}', name: 'app_subCategory_update', methods: ['EDIT'])]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_subCategory_delete', methods: ['DELETE'])]
    public function delete(SubCategory $subCategory): JsonResponse {
        $this->entityManager->remove($subCategory);
        $this->entityManager->flush();
        return $this->json("subCategory deleated", 204);
    }

}
