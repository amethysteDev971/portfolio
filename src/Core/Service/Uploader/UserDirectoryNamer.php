<?php
namespace App\Core\Service\Uploader;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class UserDirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName($object, PropertyMapping $mapping): string
    {
        // Crée un dossier basé sur l'ID de l'utilisateur
        return (string) $object->getUser()->getId();
    }
}
