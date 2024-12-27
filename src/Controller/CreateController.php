<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class CreateController extends AbstractController
{
    #[Route('/create', name: 'app_create')]
    public function index(EntityManagerInterface $em,Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setEmail('test5@gmail.com');
        $user->setPassword('azerty');
        $plainPassword = $user->getPassword();
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $em->persist($user);
        $em->flush();

        //Afficher le resultat
        $users = $em->getRepository(User::class)->findAll();


        return $this->render('create/index.html.twig', [
            'controller_name' => 'CreateController',
            'users' => $users,
        ]);
    }
}
