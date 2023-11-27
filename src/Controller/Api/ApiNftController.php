<?php 

namespace App\Controller\Api;


use App\Entity\Nft;
use App\Entity\SubCategory;
use App\Entity\User;
use App\Repository\NftRepository;
use App\Repository\SubCategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/nft')]
class ApiNftController extends AbstractController
{
    public function __construct(
        private NftRepository $nftRepository,
        private EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_all_nft', methods: ['GET'])]
    public function index(Request $request): Response
    {
      $subCategoryName = $request->query->get("sn");
      $nftTitle = $request->query->get("t");
      $qb = $this->nftRepository->findQbAll();
      if ($subCategoryName === "all" || !$subCategoryName){
        if($nftTitle === ""){
          $qb->orderBy("nft.createdAt", "desc");
        }else{
          $qb->andWhere("nft.title LIKE :nftTitle")  
            ->setParameter("nftTitle", "%" . $nftTitle . "%")
            ->orderBy("nft.createdAt", "desc");
        }
        
      }else{
        $qb->join("nft.subCategories", "s")
        ->andWhere("s.subCategoryName = :scName")
        ->setParameter("scName", $subCategoryName)
        ->orderBy("nft.createdAt", "desc");
        if ($nftTitle !== ""){
          $qb->andWhere("nft.title LIKE :nftTitle")  
          ->setParameter("nftTitle", "%" . $nftTitle . "%");
        }
        
      }

        
      $nfts = $qb->getQuery()
          ->getResult();
      return $this->json($nfts, 200, [], ['groups' => 'nft']);
        
    }

    #[Route('/show/{id}', name: 'app_show_nft', methods: ['GET'])]
    public function show(int $id): Response
    {
      $nft = $this->nftRepository->find($id);
      return $this->json($nft, 200, [], ['groups' => 'nft']);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/add', name: 'app_add_nft', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer , 
    UserRepository $userRepository,SubCategoryRepository $subCategoryRepository): JsonResponse 
    {
      $data = json_decode($request->getContent(), true);

      $user = $userRepository->find($data["user"]);


      $nft = new Nft();
      $nft->setCreatedAt(new \DateTime());
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

      try{
        $this->entityManager->persist($nft);
        $this->entityManager->flush();
      return $this->json("Nft Added with Success", 201);
      }
      catch(\Exception $e){
        return $this->json($e, 400);
      }
    }
    

  
    #[Route('/update/{id}', name: 'app_update_nft', methods: ['PUT'])]
    public function update(int $id, Request $request,NftRepository $nftRepository, 
    UserRepository $userRepository, SubCategoryRepository $subCategoryRepository, TokenInterface $token)
    {
      $data = json_decode($request->getContent(), true);
      $price = $data["price"];

      $nft = $nftRepository->find($id);
      $userConnected = $token->getUser();

      $nftOwner = $nft->getUser(); 
      $userRoles = $userConnected->getRoles();

      $nft->setPrice($price);
      $nft->setDescription($data["description"]);
      $nft->setTitle($data["title"]);
      $nft->setUser($userConnected);
      for($i = 0 ; $i<count($data["subCategories"]); $i++){
        $subCategories[] = $subCategoryRepository->findBy(["id" => $data["subCategories"][$i]]);
        $nft->addSubCategory($subCategories[$i][0]);
      }

      if(!$nft){
        return $this->json("Nft not found", 400);
      }

      if($nftOwner == $userConnected || in_array("ROLE_ADMIN" , $userRoles)){
        try{
          $this->entityManager->persist($nft);
          $this->entityManager->flush();
          return $this->json("Nft updated with Success", 201);
        }
        catch(\Exception $e){
          return $this->json($e, 400);
        }
      }else{
        return $this->json("Unauthorized", 401);
      }

    }

    #[Route('/delete/{id}', name: 'app_delete_nft', methods: ['DELETE'])]
    public function delete(int $id , NftRepository $nftRepository, TokenInterface $token): JsonResponse {

      $nft = $nftRepository->find($id);
      $userConnected = $token->getUser();
      $nftOwner = $nft->getUser(); 
      $userRoles = $userConnected->getRoles();
      if(!$nft){
        return $this->json("Nft not found", 400);
      }
      if($nftOwner == $userConnected || in_array("ROLE_ADMIN" , $userRoles)){
        try{
          $this->entityManager->remove($nft);
          $this->entityManager->flush();
          return $this->json("nft deleated", 200);
        }catch(\Exception $e){
          return $this->json($e, 400);
        }
      }
      return $this->json("Unauthorized" , 401);

    }
  }