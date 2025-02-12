<?php

namespace App\Controller\Admin;

use App\Core\Service\SectionsServices;
use App\Entity\Photo;
use App\Entity\Projets;
use App\Entity\Section;
use App\Form\admin\PhotoType;
use App\Form\admin\ProjetType;
use App\Form\admin\SectionByProjectType;
use App\Form\admin\SectionPhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Persistence\Proxy;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjetController extends AbstractController
{
    #[Route('admin/projet', name: 'app_admin_projet_list')]
    public function index(EntityManagerInterface $em): Response
    {
        $projets = $em->getRepository(Projets::class)->findAll();
        // var_dump($projets);
        // exit();
        return $this->render('admin/projet/index.html.twig', [
            'controller_name' => 'ProjetController',
            'projets' => $projets
        ]);
    }

    #[Route('/admin/projet/create', name: 'app_admin_projet_create')]
    public function create(EntityManagerInterface $em, Request $request, Security $security): Response
    {   
        $projet = new Projets();
        $projet->setUser($security->getUser());
        $section = new Section();
        //Savoir si des sections existe
        $sections = $em->getRepository(Section::class)->findAll();
        if (empty($sections)){
            $section->setRangePosition(1);
        }else{
            $section->setRangePosition(count($sections)+1);
        }

        $form = $this->createForm(ProjetType::class, $projet, [
            'action' => $this->generateUrl('app_admin_projet_create'),
            'method' => 'POST',
            'description' => '',
            'alt' => '',
            'description_photo' => '',
            'image_path' => null, // Pas d'image à afficher
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données non mappées pour la photo
            $alt = $form->get('alt')->getData();
            $description = $form->get('description_photo')->getData();
            $imageFile = $form->get('imageFile')->getData();// Traitement de l'image uploadée

            if ($imageFile) {
    
                // Créer une nouvelle entité Photo
                $photo = new Photo();
                $photo->setAlt($alt);
                $photo->setDescription($description);
                $photo->setUser($security->getUser());
                $photo->setImageFile($form->get('imageFile')->getData());
                $photo->setPath($photo->getName());
                
                $section->setPhoto($photo);
                $projet->addSection($section);
                
                //Persister la photo
                $em->persist($photo);
                $em->persist($section);
            }

            $embeddedFile = $photo->getImage();
            if ($embeddedFile) {
                $photo->setPath('uploads/photos'.'/'.$photo->getUser()->getId().'/'.$embeddedFile->getName());
            }

            $projet = $form->getData();
            $em->persist($projet);
            $em->flush();
            return $this->redirectToRoute('app_admin_projet_list');
        }
        
        return $this->render('admin/projet/new.html.twig', [
            'controller_name' => 'ProjetController',
            'form' => $form,
        ]);
    }

    #[Route('admin/projet/update/{id}', name: 'app_admin_projet_update')]
    public function updateProjet(EntityManagerInterface $em, Request $request, $id): Response
    {

    $projet = $em->getRepository(Projets::class)->find($id);

    if (!$projet) {
        throw $this->createNotFoundException('Projet non trouvé');
    }

    $sections = $projet->getSections();
    $sectionFirst = $sections[0] ?? null;
    if (!$sectionFirst) {
        throw new \Exception('Aucune section trouvée.');
    }

    $photo = $sectionFirst->getPhoto();
    if (!$photo) {
        throw new \Exception('Aucune photo trouvée pour la section.');
    }

    $form = $this->createForm(ProjetType::class, $projet, [
        'description' => $sectionFirst->getDescription() ?? '',
        'alt' => $photo->getAlt() ?? '',
        'description_photo' => $photo->getDescription() ?? '',
        'image_path' => $photo->getPath() ?? null,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $uploadedFile = $form->get('imageFile')->getData();
        //mettre à jour la section
        $sectionFirst->setDescription($form->get('description')->getData());

        if ($uploadedFile) {
            $photo->setImageFile($uploadedFile);
            $photo->setAlt($form->get('alt')->getData());
            $photo->setDescription($form->get('description_photo')->getData());
        }

        $em->persist($photo); // Persiste uniquement après toutes les modifications
    
        
        $projet = $form->getData();
        $em->persist($projet);
       
        $em->flush();

        //*Seule moyen de récupérer imageName vichUploader 
        //* met à jour après le flush
        //TODO Revenir sur l'automatisation des datas voir configuration de VichUploader
        $embeddedFile = $photo->getImage();
        if ($embeddedFile) {
            // dump($embeddedFile);
            $photo->setPath('uploads/photos'.'/'.$photo->getUser()->getId().'/'.$embeddedFile->getName());
        }
        $em->persist($photo);
        $em->flush();
        // exit();
        return $this->redirectToRoute('app_admin_projet_list');
    }
    

        return $this->render('admin/projet/new.html.twig', [
            'controller_name' => 'ProjetController update',
            'form' => $form->createView(),
        ]);
    }

    #[Route('admin/projet/delete/{id}', name: 'app_admin_projet_delete')]
    public function deleteProjet(EntityManagerInterface $em,Request $request, $id): Response
    {
        $projet = $em->getRepository(Projets::class)->find($id);
        $em->remove($projet);
        $em->flush();

        return $this->redirectToRoute('app_admin_projet_list');
    }

    #[Route('admin/projet/delete/section/{id}', name: 'app_admin_projet_delete_section')]
    public function deleteSection(EntityManagerInterface $em, $id): Response
    {
        $section = $em->getRepository(Section::class)->find($id);
        if (!$section) {
            throw $this->createNotFoundException('Section non trouvée');
        }

        $em->remove($section);
        $em->flush();

        return $this->redirectToRoute('app_admin_projet_list');
    }

    #[Route('admin/projet/show/{id}', name: 'app_admin_projet_show')]
    public function showProjet(EntityManagerInterface $em, Request $request, $id, SectionsServices $sectionsServices): Response
    {
        // Récupérer le projet par son ID
        $projet = $em->getRepository(Projets::class)->find($id);

        // Vérifier si le projet existe
        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        // Convertir la collection en tableau
        $sections = $projet->getSections();
        $sectionsArray = $sections->toArray();

        // Trier le tableau
        usort($sectionsArray, array($sectionsServices, 'asortSectionByRangePosition'));

        // Vider les sections existantes et ajouter les nouvelles sections triées
        $projet->getSections()->clear();
        foreach ($sectionsArray as $section) {
            $projet->addSection($section);
        }

        // dump(phpinfo());
        // exit();

        // Rendre la vue show.html.twig avec les informations du projet
        return $this->render('admin/projet/show.html.twig', [
            'controller_name' => 'Projet ' . $projet->getTitle(),
            'projet' => $projet,
            'sections' => $sectionsArray,
        ]);
    }

    #[Route('admin/projet/list/{id}', name: 'admin_sections_list')]
    public function sectionsList(EntityManagerInterface $em, $id): Response
    {
        $projet = $em->getRepository(Projets::class)->find($id);

        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $sections = $projet->getSections();

        return $this->render('admin/sections/list.html.twig', [
            'sections' => $sections,
            'projet' => $projet,
        ]);
    }

    #[Route('/admin/projet/form', name: 'admin_section_form')]
    public function sectionForm(): Response
    {
        // Logique pour afficher le formulaire d'ajout de section
        return $this->render('admin/projet/sections_form.html.twig');
    }

    #[Route('admin/projet/modal/{id}/{action}', name: 'admin_projet_modal')]
    public function loadModalContent(EntityManagerInterface $em, $id, $action, 
                                    SectionsServices $sectionsServices, Request $request,
                                    Security $security): Response
    {
        try {
            // Récupérer le projet par son ID
            $projet = $em->getRepository(Projets::class)->find($id);
            $sections = $projet->getSections();
            $sectionsArray = $sections->toArray();

            // Vérifier si le projet existe
            if (!$projet) {
                throw $this->createNotFoundException('Projet non trouvé');
            }

            if ($action ==='add_section') {
               // Convertir la collection en tableau
                
                

                // Trier le tableau
                usort($sectionsArray, array($sectionsServices, 'asortSectionByRangePosition')); 
            }
            

            //Pour le formulaire Add section
            if ($action ==='add_section') {
                $section = new Section();
                $form = $this->createForm(SectionByProjectType::class, $section,[
                    'action' => $this->generateUrl('admin_projet_modal',[
                        'id' => $projet->getId(),
                        'action' => 'add_section'
                    ]),
                    'method' => 'POST',
                ]);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    // Récupérer les données non mappées pour la photo
                    $alt = $form->get('alt')->getData();
                    $description = $form->get('description_photo')->getData();
                    $imageFile = $form->get('imageFile')->getData();
                    // dump($imageFile);
                    // exit();
                    if ($imageFile) {
                        // Gérer l'upload de l'image
                        // $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                        // $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
            
                        // Créer une nouvelle entité Photo
                        $photo = new Photo();
                        $photo->setAlt($alt);
                        $photo->setDescription($description);
                        $photo->setImageFile($form->get('imageFile')->getData());
                        // $photo->setPath($newFilename);
                        $photo->setMimeType($imageFile->getClientMimeType());
                        $photo->setSize($imageFile->getSize());
                        $photo->setUser($security->getUser());
                        $newFilename = 'public/uploads/photos'.'/'.$photo->getUser()->getId().'/'.uniqid() . '.' . $imageFile->guessExtension();
                        $photo->setPath($newFilename);
            
                        // Associer la photo à la section
                        $section->setPhoto($photo);
                        //persiter range_positions
                        $section->setRangePosition($sectionsServices->getLastRangePosition($sectionsArray)+1);
                        $section->setProjets($projet);

                        // dump($photo);
                        // dump(uniqid());
                        // dump($photo->getUser()->getId());
                        // exit();
        
                        //Persister la photo
                        $em->persist($photo);
                        
                    }
                    // dump($section);
                    // exit();
                    // Persister la section
                    $em->persist($section);
                    $em->flush();
                    return $this->redirectToRoute('app_admin_projet_show',[
                        'id' => $projet->getId()
                    ]);
                }//fin du if formulaire
            }


            // Rendre le contenu approprié en fonction de l'action
            switch ($action) {
                case 'reorganize':
                    return $this->render('admin/projet/sections_list.html.twig', [
                        'projet' => $projet,
                        'sections' => $sectionsArray,
                        'action' => $action,
                    ]);
                case 'add_section':
                    // dump($projet);
                    // exit();
                    return $this->render('admin/projet/section_form.html.twig', [
                        'projet' => $projet,
                        'action' => $action,
                        'form' => $form,
                    ]);
                default:
                    throw new \Exception('Action non reconnue action reçue = '.$action);
            }
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
