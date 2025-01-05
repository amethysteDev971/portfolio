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
        // Vérifie que l'objet a une méthode getUser et que l'utilisateur existe
        if (!method_exists($object, 'getUser') || null === $object->getUser()) {
            throw new \RuntimeException('L\'objet doit être associé à un utilisateur.');
        }

        // Récupère le fichier téléchargé
        $file = $mapping->getFile($object);
        if (!$file instanceof File) {
            throw new \RuntimeException('Le fichier téléchargé est invalide.');
        }

        // Génére un nom unique pour le fichier
        $extension = $file->guessExtension() ?? 'bin'; // Par défaut "bin" si aucune extension
        return uniqid() . '.' . $extension; // Ne retourne que le nom du fichier
    }
}