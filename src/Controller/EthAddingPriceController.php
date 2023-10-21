<?php 

namespace App\Controller;

use App\Entity\Eth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class EthAddingPriceController extends AbstractController{

  #[Route("eth-add-price")]
public function getPrice(HttpClientInterface $httpClient , EntityManagerInterface $entityManager){

  //  $response = $httpClient->request("GET" , "https://min-api.cryptocompare.com/data/price?fsym=ETH&tsyms=EUR");
  //  $currentPrice = $response->getContent();
  //  $currentPrice = json_decode($currentPrice , true);
  //  $currentPriceEuro = $currentPrice["EUR"];

  $currentPriceEuro = rand(100000 , 130000)/100;

  $date = new \DateTime("now" , new \DateTimeZone("Europe/Paris"));

  $Eth = new Eth();
  $Eth->setCreatedAt($date);
  $Eth->setEthValue($currentPriceEuro);

  try{
      $entityManager->persist($Eth);
      $entityManager->flush(); 

      return $this->json("Price Updated" , 200);
    }catch(\Exception $e){
      return $this->json($e->getMessage() , 403);
    }




}

}