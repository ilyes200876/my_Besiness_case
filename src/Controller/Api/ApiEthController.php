<?php

namespace App\Controller\Api;

use App\Repository\EthRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/eth')]
class ApiEthController extends AbstractController
{
    #[Route('/', name: 'app_api_eth', methods: ['GET'])]
    public function show(EthRepository  $ethRepository): Response
    {
        $eths = $ethRepository->findQbAll()
            ->setMaxResults(7)
            ->orderBy("eth.createdAt", "desc")
            ->getQuery()
            ->getResult();

        return $this->json($eths, 200);
    }
}
