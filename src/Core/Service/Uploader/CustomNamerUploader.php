<?php

namespace App\Core\Service\Uploader;

use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\HttpFoundation\File\File;

class CustomNamerUploader implements NamerInterface
{
    /**
     * Crée un nom pour le fichier téléchargé.
     *
     * @param object          $object  L'objet auquel l'upload est attaché
     * @param PropertyMapping $mapping Le mapping à utiliser pour manipuler l'objet donné
     *
     * @return string Le nom du fichier
     */
    public function name(object $object, PropertyMapping $mapping): string
    {
        // Ici, tu accèdes à l'objet (par exemple, à l'utilisateur associé à l'image)
        $userId = $object->getUser()->getId();
        
        // Tu génères un nom unique pour le fichier
        $file = $mapping->getFile($object); // Obtient le fichier via le PropertyMapping
        return $userId . '/' . uniqid() . '.' . $file->guessExtension();
    }
}