<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\admin\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostsController extends AbstractController
{
    #[Route('/admin/posts', name: 'app_admin_posts')]
    public function index(EntityManagerInterface $em): Response
    {

        $posts = $em->getRepository(Post::class)->findAll();
        
        return $this->render('admin/posts/index.html.twig', [
            'controller_name' => 'PostsController',
            'posts' => $posts,
        ]);
    }

    #[Route('/admin/posts/create', name: 'app_admin_posts_create', methods:['POST','GET'])]
    public function create(EntityManagerInterface $em, Request $request): Response
    {

        $post = new Post();
        $form = $this->createForm(PostType::class, $post,[
            'action' => $this->generateUrl('app_admin_posts_create'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $post = $form->getData();
            $post->setCreatedAt(new \DateTime('now'));
            $post->setPublished(false);
            // dump($post);

            // ... perform some action, such as saving the task to the database
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_admin_posts');
        }
        
        return $this->render('admin/posts/new.html.twig', [
            'controller_name' => 'PostsController',
            'form' => $form,
        ]);
    }
}
