<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Form\admin\PhotoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PhotoController extends AbstractController
{
    #[Route('/admin/photo', name: 'app_photo')]
    public function index(): Response
    {
        return $this->render('admin/photo/index.html.twig', [
            'controller_name' => 'PhotoController',
        ]);
    }

    #[Route('/admin/photo/create', name: 'app_photo_create')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {

        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo,[
            'action' => $this->generateUrl('app_photo_create'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $post = $form->getData();
            // $post->setCreatedAt(new \DateTime('now'));
            // $post->setPublished(false);
            // dump($post);
            dump($photo);
            // ... perform some action, such as saving the task to the database
            $em->persist($photo);
            $em->flush();

            return $this->redirectToRoute('app_photo_create');
        }
        
        return $this->render('admin/photo/new.html.twig', [
            'controller_name' => 'PhotoController',
            'form' => $form,
        ]);
    }
}
