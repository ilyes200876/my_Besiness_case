<?php
namespace App\Service;

use App\Entity\Nft;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class FileUploaderApi
{
  public function __construct(
    private SluggerInterface $slugger,
    private ParameterBagInterface $parameterBag,
    private TokenInterface $token

  ) {
  }

  public function upload(UploadedFile $file, string $title, string $description, int $price): Nft
  {
    $uploadDirectory = $this->parameterBag->get('image_directory');
    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $this->slugger->slug($originalFilename);
    $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

    $file->move(
      $uploadDirectory,
      $fileName
  );

  $user = $this->token->getUser();

  $nft = new Nft();
  $nft->setSrc($fileName);
  $nft->setTitle($title);
  $nft->setDescription($description);
  $nft->setCreatedAt(new \DateTime());
  $nft->setPrice($price);
  $nft->setUser($user);

    return $nft;
  }

}