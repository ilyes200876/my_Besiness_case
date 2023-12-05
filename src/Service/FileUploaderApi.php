<?php
namespace App\Service;

use App\Entity\Nft;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\Date;

class FileUploaderApi
{
  public function __construct(
    private string $targetDirectory,
    private SluggerInterface $slugger,
    private ParameterBagInterface $parameterBag,

  ) {
  }

  public function upload(UploadedFile $file): Nft
  {
    $uploadDirectory = $this->parameterBag->get('image_directory');
    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $this->slugger->slug($originalFilename);
    $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

    $file->move(
      $uploadDirectory,
      $fileName
  );

  $nft = new Nft();
  $nft->setSrc($fileName);
  // $nft->setTitle($title);
  // $nft->setDescription($description);
  // $nft->setCreatedAt($createdAt);

    return $nft;
  }



  public function getTargetDirectory(): string
  {
    return $this->targetDirectory;
  }
}