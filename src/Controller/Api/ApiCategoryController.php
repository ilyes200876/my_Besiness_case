<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/category')]
class ApiCategoryController extends AbstractController
{
    
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_all_categogy', methods: ['GET'])]
    public function index(): Response
    {
        
        $categories = $this->categoryRepository->findAll();

        return $this->json($categories, 200, [], ['groups' => 'allCategories']);
        
    }

    #[Route('/show/{id}', name: 'app_show_categogy', methods: ['GET'])]
    public function show(int $id): Response
    {
        $category = $this->categoryRepository->find($id);

        return $this->json($category, 200, [], ['groups' => 'oneCategory']);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/add', name: 'app_add_category', methods: ['POST'])]
    public function add(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        
        $category = new Category();
        $category->setCategoryName($data["categoryName"]);
        
        try{
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            return $this->json("Category Added with Success", 201);
        }
        catch(\Exception $e){
            return $this->json($e, 400);
        }
        
    }
    
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/update/{id}', name: 'app_update_category', methods: ['put'])]
    public function update(Request $request, CategoryRepository $categoryRepository, int $id)
    {
        $data= json_decode($request->getContent(), true);
        $category = $categoryRepository->find($id);

        $category->setCategoryName($data["categoryName"]);

        try{
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            return $this->json("Category updated with success", 201);
        }catch(\Exception $e){
            return $this->json($e, 400);
        }

    }

    // #[IsGranted("ROLE_ADMIN")]
    #[Route('/delete/{id}', name: 'app_delete_category', methods: ['delete'])]
    public function delete(Category $category): JsonResponse {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
        return $this->json("category deleated", 204);
    }

}
