<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Form\admin\PhotoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PhotoController extends AbstractController
{
    #[Route('/admin/photo', name: 'app_photo')]
    public function index(EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, UploaderHelper $helper): Response
    {
        $photos = $em->getRepository(Photo::class)->findAll();
        // dump($photos);
        

        return $this->render('admin/photo/index.html.twig', [
            'controller_name' => 'PhotoController',
            'photos' => $photos,
        ]);
    }

    #[Route('/admin/photo/create', name: 'app_photo_create')]
    public function create(EntityManagerInterface $em, Request $request, Security $security): Response
    {

        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo,[
            'action' => $this->generateUrl('app_photo_create'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dump($photo->getImage());  // Maintenant, cela ne devrait pas être null
            // exit();

            // Définissez le chemin du répertoire utilisateur
            // $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/photos/' . $userId;

            $photo = $form->getData();
            $photo->setImageFile($form->get('imageFile')->getData());
            // dump($photo);
            // exit();
            $photo->setUser($security->getUser());
            // Ne pas oublier de gérer l'image (upload etc.)
    
            $em->persist($photo);
            $em->flush();
    
            return $this->redirectToRoute('app_photo');
        }
    
        
        
        return $this->render('admin/photo/new.html.twig', [
            'controller_name' => 'PhotoController',
            'form' => $form,
        ]);
    }
}
