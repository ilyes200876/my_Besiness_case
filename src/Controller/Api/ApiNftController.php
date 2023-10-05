<?php 

namespace App\Controller\Api;


use App\Entity\Nft;
use App\Entity\SubCategory;
use App\Entity\User;
use App\Repository\NftRepository;
use App\Repository\SubCategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/nft')]
class ApiNftController extends AbstractController
{
    public function __construct(
        private NftRepository $nftRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_nft_all', methods: ['GET'])]
    public function index(): Response
    {
        
        $nfts = $this->nftRepository->findAll();

        return $this->json($nfts, 200, [], ['groups' => 'allNfts']);
        
    }

    #[Route('/show/{id}', name: 'app_nft_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $nft = $this->nftRepository->find($id);

        return $this->json($nft, 200, [], ['groups' => 'oneNft']);
    }

    #[Route('/add', name: 'app_nft_add', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer , UserRepository $userRepository,SubCategoryRepository $subCategoryRepository): JsonResponse {

      $data = json_decode($request->getContent(), true);

      $user = $userRepository->find($data["user"]);


        $nft = new Nft();
        $nft->setCreatedAt(new \DateTime($data["createdAt"]));
        $nft->setPrice($data["price"]);
        $nft->setDescription($data["description"]);
        $nft->setFormat($data["format"]);
        $nft->setSrc($data["src"]);
        $nft->setTitle($data["title"]);
        $nft->setWeight($data["weight"]);
        $nft->setUser($user);
        $subCategories = [];
        for($i = 0 ; $i<count($data["subCategories"]); $i++){
          $subCategories[] = $subCategoryRepository->findBy(["id" => $data["subCategories"][$i]]);
          $nft->addSubCategory($subCategories[$i][0]);
        }
        $this->entityManager->persist($nft);
        $this->entityManager->flush();

        return $this->json("Nft Added with Success", 201);
    }
    

    #[Route('/update/{id}', name: 'app_nft_update', methods: 'PUT')]
    public function update()
    {

    }

    #[Route('/delete/{id}', name: 'app_nft_delete', methods: 'DELETE')]
    public function delete(Nft $nft): JsonResponse {
        $this->entityManager->remove($nft);
        $this->entityManager->flush();
        return $this->json("nft deleated", 204);
    }
  }