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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/subCategory')]
class ApiSubCategoryController extends AbstractController
{
    public function __construct(
        private SubCategoryRepository $subCategoryRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_all_subCategogy', methods: ['GET'])]
    public function index(): Response
    {
        $subCategories = $this->subCategoryRepository->findAll();

        return $this->json($subCategories, 200, [], ['groups' => 'allSubCategories']);
        
        
    }

    #[Route('/show/{id}', name: 'app_show_subCategogy', methods: ['GET'])]
    public function show(int $id): Response
    {
        $subCategory = $this->subCategoryRepository->find($id);

        return $this->json($subCategory, 200, [], ['groups' => 'oneSubCategory']);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/add', name: 'app_add_subCategory', methods: ['POST'])]
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
            return $this->json('SubCategory added with success', 201);
        }
        catch(\Exception $e){
            return $this->json($e, 400);
        }
        

    }
    
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/update/{id}', name: 'app_update_subCategory', methods: ['PUT'])]
    public function update(Request $request, SubCategoryRepository $subCategoryRepository, int $id)
    {

        $data = json_decode($request->getContent(), true);
        $subCategory = $subCategoryRepository->find($id);

        $subCategory->setSubCategoryName($data["subCategoryName"]);
        
        if($subCategory){
            try{
                $this->entityManager->persist($subCategory);
                $this->entityManager->flush();
                return $this->json("subCategory updated with success", 201);
            }catch(\Exception $e){
                return $this->json($e, 400);
            }
        }else{
            return $this->json("Subcategory not found", 401);
        }

    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/delete/{id}', name: 'app_delete_subCategory', methods: ['DELETE'])]
    public function delete(int$id, SubCategoryRepository $subCategoryRepository): JsonResponse {
        $subCategory = $subCategoryRepository->find($id);
        if ($subCategory){
            $this->entityManager->remove($subCategory);
            $this->entityManager->flush();
            return $this->json("SubCategory deleated", 204);
        }else{
            return $this->json("SubCategory  not found", 400);
        }
    }

}
