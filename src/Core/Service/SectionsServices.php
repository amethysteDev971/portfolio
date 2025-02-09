<?php

namespace App\Core\Service;

use App\Entity\Section;

class SectionsServices
{
    public function __construct()
    {
        // Constructor code here
    }

    public function asortSectionByRangePosition($a, $b){
      if ($a->getRangePosition() == $b->getRangePosition()) return 0;
        return ($a->getRangePosition() < $b->getRangePosition()) ? -1 : 1;
    }

/**
 * Retourne la valeur maximale de "range_position" parmi une collection d'objets Section.
 *
 * @param iterable $sections Une collection ou un tableau d'objets Section.
 * @return int
 * @throws \InvalidArgumentException si le paramètre n'est ni un tableau ni un objet convertissable en tableau.
 */
public function getLastRangePosition(iterable $sections): int
{
    // Convertir en tableau si nécessaire
    if (is_array($sections)) {
        $sectionsArray = $sections;
    } elseif (method_exists($sections, 'toArray')) {
        $sectionsArray = $sections->toArray();
    } else {
        throw new \InvalidArgumentException('Le paramètre sections doit être un tableau ou un objet avec la méthode toArray().');
    }
    
    // Si le tableau est vide, on retourne 1 (ou une autre valeur par défaut)
    if (empty($sectionsArray)) {
        return 1;
    }
    
    // Extraction de la propriété "range_position" pour chaque Section.
    // En utilisant un getter (ici getRangePosition()) pour respecter l'encapsulation.
    $rangePositions = array_map(function(Section $section) {
        return $section->getRangePosition(); // ou $section->range_position si la propriété est publique
    }, $sectionsArray);
    
    // Retourne la valeur maximale
    return max($rangePositions);
}

    public function getAllSections()
    {
        // Code to get all sections
    }

    public function getSectionById($id)
    {
        // Code to get a section by its ID
    }

    public function createSection($data)
    {
        // Code to create a new section
    }

    public function updateSection($id, $data)
    {
        // Code to update an existing section
    }

    public function deleteSection($id)
    {
        // Code to delete a section
    }
}