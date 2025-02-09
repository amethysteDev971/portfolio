<?php

namespace App\Controller\Admin;

use App\Core\Service\SectionsServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Photo;
use App\Entity\Projets;
use App\Entity\Section;
use App\Form\admin\SectionPhotoType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\Length;

class SectionController extends AbstractController
{
    #[Route('admin/section', name: 'app_admin_section_list')]
    public function index(EntityManagerInterface $em, 
                        UrlGeneratorInterface $urlGenerator, 
                        UploaderHelper $helper, 
                        KernelInterface $kernel, 
                        LoggerInterface $logger,
                        SectionsServices $sectionsServices): Response
    {

        //Sections par projets
        $projets = $em->getRepository(Projets::class)->findAll();
        // foreach ($projets as $key => $value) {
        //     dump($value);
        // }

        // $logger->info('Test log: ceci est un test');
        // $logDir = $kernel->getLogDir();
        // dump($logDir); // Affiche le chemin du dossier de logs
        // exit;
        $sections = $em->getRepository(Section::class)->findAll();
        
        // dump($sections);
        // exit();
        $clones = [];
        for ($i=0; $i < count($sections); $i++) { 
            if ($sections[$i]->getRangePosition()) {
                $clones[$sections[$i]->getRangePosition()-1] = $sections[$i];
            }
        }
        $sections = $clones;
        // sort($sections);
        // usort($sections, 'self::asort_util');
        usort($sections, array($sectionsServices, 'asortSectionByRangePosition'));
        
        
        // dump($clones);
        // dump($sections);
        // exit();

        return $this->render('admin/section/index.html.twig', [
            'controller_name' => 'SectionController',
            'sections' => $sections,
            'projets' => $projets
        ]);
    }

    #[Route('admin/section/show/{id}', name: 'app_admin_section_show')]
    public function show(EntityManagerInterface $em, 
                        UrlGeneratorInterface $urlGenerator, 
                        UploaderHelper $helper, 
                        KernelInterface $kernel, 
                        LoggerInterface $logger,
                        SectionsServices $sectionsServices, 
                        $id): Response
    {
        $section = $em->getRepository(Section::class)->find($id);
        // dump($section);
        // exit();
        // Rendre la vue show.html.twig avec les informations du projet
        return $this->render('admin/section/show.html.twig', [
            'controller_name' => 'Section ' ,
            'section' => $section,
            // 'sections' => $sectionsArray,
        ]);
    }

    #[Route('admin/section/update/{id}', name: 'app_admin_section_update')]
    public function update(EntityManagerInterface $em, 
                        UrlGeneratorInterface $urlGenerator, 
                        UploaderHelper $helper, 
                        KernelInterface $kernel, 
                        LoggerInterface $logger,
                        SectionsServices $sectionsServices, 
                        $id): Response
    {
        $section = $em->getRepository(Section::class)->find($id);
        // dump($section);
        // exit();
        // Rendre la vue show.html.twig avec les informations du projet
        return $this->render('admin/section/update.html.twig', [
            'controller_name' => 'Section ' ,
            'section' => $section,
            // 'sections' => $sectionsArray,
        ]);
    }

    static function asort_util ($a, $b) {
        if ($a->getRangePosition() == $b->getRangePosition()) return 0;
        return ($a->getRangePosition() < $b->getRangePosition()) ? -1 : 1;
    }

    #[Route('/admin/section/create', name: 'app_admin_section_create')]
    public function create(EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $section = new Section();

        $form = $this->createForm(SectionPhotoType::class, $section,[
            'action' => $this->generateUrl('app_admin_section_create'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        //Savoir si des sections existe
        $sections = $em->getRepository(Section::class)->findAll();
        if (empty($sections)){
            $section->setRangePosition(1);
        }else{
            $section->setRangePosition(count($sections)+1);
        }

        if ($section === null) {

            // $section->setRangePosition()
        }

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

            return $this->redirectToRoute('app_admin_section_list');
        }
        
        return $this->render('admin/section/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update-section-order', name: 'update_section_order', methods: ['POST'])]
    public function updateSectionOrder(Request $request, EntityManagerInterface $em, LoggerInterface $logger): JsonResponse
    {
        // Log brut du contenu de la requête
        $logger->info('Raw request content', ['content' => $request->getContent()]);

        $data = json_decode($request->getContent(), true);

        // Log des données décodées
        $logger->info('Decoded request data', ['decoded_data' => $data]);

        if (!isset($data['order']) || !is_array($data['order'])) {
            $logger->error('Invalid data received', ['data' => $data]);
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        foreach ($data['order'] as $position => $id) {
            $logger->info("Processing section", ['id' => $id, 'position' => $position]);
            $section = $em->getRepository(Section::class)->find($id);
            if ($section) {
                $section->setRangePosition($position+1); // 1-based index
                $em->persist($section);
            } else {
                $logger->warning("Section not found", ['id' => $id]);
            }
        }

        $em->flush();

        $logger->info('Database changes saved successfully.');
        return new JsonResponse(['success' => true]);
    }

    // #[Route('/admin/sections/list', name: 'admin_sections_list')]
    // public function sectionsList(): Response
    // {
    //     // Your logic to list sections
    //     return new Response('Sections list');
    // }

    // #[Route('/admin/section/form', name: 'admin_section_form')]
    // public function sectionForm(): Response
    // {
    //     // Your logic to show the section form
    //     return new Response('Section form');
    // }

}
