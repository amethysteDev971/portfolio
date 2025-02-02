<?php

namespace App\Core\Service;

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