<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;

/**
 * @method User|null getUser()
 */
abstract class BaseController extends AbstractController
{
    // Vous pouvez ajouter ici des méthodes ou propriétés communes à vos contrôleurs
}
