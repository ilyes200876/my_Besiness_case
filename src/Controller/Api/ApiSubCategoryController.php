<?php

namespace App\Controller\Api;

use App\Entity\SubCategory;
use App\Entity\Category;
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
    public function add(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $subCategory = new SubCategory();

        $subCategory->setSubCategoryName($data['subCategoryName']);
        // dd($subCategory);
        $category = $data['category->getId()'];
        dd($category);
        // $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
        // if (!$category) {
        //     return new JsonResponse(['message' => 'La catégorie avec l\'ID donné n\'a pas été trouvée.'], 404);
        // }
    
        // Associez la catégorie à la sous-catégorie
        $subCategory->setCategory($category);
        // $subCategory->setCategory($data['category']);
        dd($subCategory);

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
