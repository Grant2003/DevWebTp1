<?php

namespace App\Classes;
//-----------------------------------
//   Fichier : ImageProduit.php
//   Par:      Anthony Grenier
//   Date :    2025-3-29
//   Modification :    2025-5-07
//-----------------------------------
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageProduit{
   private $produitId;
   private $imageProduit;

   public function getProduitId()
   {
      return $this->produitId;
   }
   
   public function setProduitId($id)
   {
       $this->produitId = $id ;
   }
 

  public function getImageProduit() : ?UploadedFile
  {
     return $this->imageProduit;
  }

  public function setImageProduit(UploadedFile $fichier)
  {
    $this->imageProduit = $fichier;
  }

  //televersement dans le catalogue
    public function televerserCatalogue(&$codeErr)
    {
        if ($this->imageProduit !== null) {
            $mime = $this->imageProduit->getClientMimeType();

            $sourceImage = null;

            //gestion des different formats
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceImage = imagecreatefromjpeg($this->imageProduit->getPathname());
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($this->imageProduit->getPathname());
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($this->imageProduit->getPathname());
                    break;
                default:
                    $codeErr = -1; 
                    return false;
            }

            if (!$sourceImage) {
                $codeErr = -2; 
                return false;
            }

            $nomDossier = __DIR__ . '/../../public/images/produits';
            $nomFichier = "$this->produitId.jpg";

            if (!file_exists($nomDossier)) {
                mkdir($nomDossier, 0775, true);
            }

            imagejpeg($sourceImage, $nomDossier . '/' . $nomFichier, 85);
            imagedestroy($sourceImage);

            return true;
        }

        $codeErr = -3; 
        return false;
    }
  //televersement dans le detail

    public function televerserDetail(&$codeErr)
    {
        if ($this->imageProduit !== null) {
            $mime = $this->imageProduit->getClientMimeType();

            $sourceImage = null;

            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceImage = imagecreatefromjpeg($this->imageProduit->getPathname());
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($this->imageProduit->getPathname());
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($this->imageProduit->getPathname());
                    break;
                default:
                    $codeErr = -1; 
                    return false;
            }

            if (!$sourceImage) {
                $codeErr = -2; 
                return false;
            }

            $nomDossier = __DIR__ . '/../../public/images/descriptions';
            $nomFichier = "$this->produitId.jpg";

            if (!file_exists($nomDossier)) {
                mkdir($nomDossier, 0775, true);
            }

            imagejpeg($sourceImage, $nomDossier . '/' . $nomFichier, 85);
            imagedestroy($sourceImage);

            return true;
        }

        $codeErr = -3; 
        return false;
    }
}

