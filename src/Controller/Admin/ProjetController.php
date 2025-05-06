<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Core\Service\SectionsServices;
use App\Entity\Photo;
use App\Entity\Projets;
use App\Entity\Section;
use App\Entity\User;
use App\Form\admin\PhotoType;
use App\Form\admin\ProjetType;
use App\Form\admin\SectionByProjectType;
use App\Form\admin\SectionPhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Persistence\Proxy;
use PhpParser\Builder\Property;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

class ProjetController extends BaseController
{
    private $storage;
    private $propertyMappingFactory;

    public function __construct(StorageInterface $storage, PropertyMappingFactory $propertyMappingFactory)
    {
        $this->storage = $storage;
        $this->propertyMappingFactory = $propertyMappingFactory;    
    }

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
    public function create(
        EntityManagerInterface $em,
        Request $request,
        Security $security
    ): Response {
        $user    = $security->getUser();
        $projet  = new Projets();
        $projet->setUser($user);

        // On prépare la première section
        $section = new Section();
        $count   = $em->getRepository(Section::class)->count([]);
        $section->setRangePosition($count + 1);
        $projet->addSection($section);

        // Pas encore de photo attachée => on prépare un Photo vierge
        $photo = $section->getPhoto();
        if (null === $photo) {
            $photo = new Photo();
            $photo->setUser($user);
            $section->setPhoto($photo);
        }

        // Création du formulaire exactement comme pour update
        $form = $this->createForm(ProjetType::class, $projet, [
            'action'            => $this->generateUrl('app_admin_projet_create'),
            'method'            => 'POST',
            'alt'               => $photo->getAlt(),
            'description_photo' => $photo->getDescription(),
            'image_path'        => $photo->getUrl(),  // votre getter url()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de la description de la section
            $section->setDescription($form->get('description')->getData());

            /** @var UploadedFile|null $uploadedFile */
            $uploadedFile = $form->get('imageFile')->getData();
            if ($uploadedFile) {
                // VichUploader gère l’upload et la suppression de l’ancien fichier
                $photo->setImageFile($uploadedFile);
                $photo->setAlt($form->get('alt')->getData());
                $photo->setDescription($form->get('description_photo')->getData());
            }

            // Grâce au cascade persist/remove sur Section::$photo, un seul persist + flush suffit
            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('app_admin_projet_list');
        }

        return $this->render('admin/projet/new.html.twig', [
            'controller_name' => 'ProjetController create',
            'form'            => $form->createView(),
            'projet'          => $projet,
        ]);
    }



    #[Route('admin/projet/update/{id}', name: 'app_admin_projet_update')]
    public function updateProjet(
        EntityManagerInterface $em,
        Request $request,
        Security $security,
        $id
    ): Response {
        $user   = $security->getUser();
        $projet = $em->getRepository(Projets::class)->find($id);

        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        // On récupère (ou crée) la première section
        $sections     = $projet->getSections();
        $section      = $sections->first();
        if (!$section) {
            throw new \Exception('Aucune section trouvée.');
        }

        // On récupère (ou prépare) la Photo liée à cette section
        $photo = $section->getPhoto();
        if (!$photo) {
            $photo = new Photo();
            $photo->setUser($user);
            $section->setPhoto($photo);
        }

        // On monte le formulaire exactement comme en create
        $form = $this->createForm(ProjetType::class, $projet, [
            'action'            => $this->generateUrl('app_admin_projet_update', ['id' => $id]),
            'method'            => 'POST',
            'alt'               => $photo->getAlt(),
            'description_photo' => $photo->getDescription(),
            'image_path'        => $photo->getUrl(), // via votre getter url()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On met à jour la description de la section
            $section->setDescription($form->get('description')->getData());

            /** @var UploadedFile|null $uploadedFile */
            $uploadedFile = $form->get('imageFile')->getData();
            if ($uploadedFile) {
                // Vich va gérer le remplacement et la suppression ancienne image
                $photo->setImageFile($uploadedFile);
                $photo->setAlt($form->get('alt')->getData());
                $photo->setDescription($form->get('description_photo')->getData());
            }

            // Grâce au cascade persist/remove sur Section::$photo, un seul flush suffit
            $em->flush();

            return $this->redirectToRoute('app_admin_projet_list');
        }

        return $this->render('admin/projet/new.html.twig', [
            'controller_name' => 'ProjetController update',
            'form'            => $form->createView(),
            'projet'          => $projet
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

    #[Route(
        '/admin/projet/delete/section/modal/{projectId}/{sectionId}',
        name: 'app_admin_projet_delete_section_modal',
        methods: ['DELETE']
    )]
    public function deleteSectionModal(
        EntityManagerInterface $em,
        int $projectId,
        int $sectionId
    ): Response {
        $projet = $em->getRepository(Projets::class)->find($projectId);
        if (!$projet) {
            throw new NotFoundHttpException('Projet non trouvé');
        }
    
        $section = $em->getRepository(Section::class)->find($sectionId);
        if (
            !$section
            || null === $section->getProjets()
            || $section->getProjets()->getId() !== $projet->getId()
        ) {
            throw new NotFoundHttpException('Section non trouvée pour ce projet');
        }
    
        $em->remove($section);
        $em->flush();
    
        return new Response('Section supprimée avec succès', Response::HTTP_OK);
    }

    #[Route('admin/projet/show/{id}', name: 'app_admin_projet_show')]
    public function showProjet(EntityManagerInterface $em, Request $request, $id, SectionsServices $sectionsServices, LoggerInterface $logger): Response
    {
        $logger->info('🔥 TEST LOG');
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
        // dump($projet);
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

            if ($action ==='reorganize' || $action ==='delete_section') {
                // Récupérer les sections du projet
                $sections = $projet->getSections();
                // dump($sections);
                // exit();
                // Vérifier si la collection de sections est vide
                if ($sections->isEmpty()) {
                    throw new \Exception('Aucune section trouvée pour ce projet.');
                }
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

                        //TODO EmbededFile do not exist anymore
                        // $photo->setMimeType($imageFile->getClientMimeType());
                        // $photo->setSize($imageFile->getSize());
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
                case 'delete_section':
                    // $sectionId = $request->request->get('section_id');
                    // $section = $em->getRepository(Section::class)->find($sectionId);
                    // if (!$section) {
                    //     throw new NotFoundHttpException('Section non trouvée');
                    // }
                    // $em->remove($section);
                    // $em->flush();
                    // return new Response('Section supprimée avec succès', Response::HTTP_OK);
                    return $this->render('admin/projet/sections_list_delete.html.twig', [
                        'projet' => $projet,
                        'sections' => $sectionsArray,
                        'action' => $action,
                    ]);
                default:
                    throw new \Exception('Action non reconnue action reçue = '.$action);
            }
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    

    #[Route('/upload-cropped-image', name: 'upload_cropped_image', methods: ['POST'])]
    public function uploadCroppedImage(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        LoggerInterface $logger
    ): JsonResponse {
        $projetId    = $request->request->get('projet_id');
        $uploadedFile = $request->files->get('croppedImage');

        $logger->info('🔥 Début de upload_cropped_image', [
            'projectId' => $projetId,
            'user'      => $this->getUser()->getId(),
        ]);

        if (!$uploadedFile instanceof UploadedFile) {
            return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }

        $projet = $em->getRepository(Projets::class)->find($projetId);
        if (!$projet) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $security->getUser();
        if (! $user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        // Créer la Photo et l'associer
        $photo = new Photo();
        $photo->setUser($user);
        $photo->setImageFile($uploadedFile);
        $photo->setAlt('Cover image for project '.$projet->getTitle());
        $photo->setDescription('Cover image uploaded for project');

        $projet->setCoverPhoto($photo);

        $em->persist($photo);
        $em->persist($projet);
        $em->flush();

        // Récupère l'URL publique générée par VichUploader
        $fileUrl = $this->storage->resolveUri($photo, 'imageFile');

        return new JsonResponse(['url' => $fileUrl]);
    }

    #[Route('/upload-cropped-image-update', name: 'upload_cropped_image_update', methods: ['POST'])]
    public function updateUploadCroppedImage(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        LoggerInterface $logger
    ): JsonResponse {
        $projetId     = $request->request->get('projet_id');
        $uploadedFile = $request->files->get('croppedImage');

        $logger->info('🔥 Début de upload_cropped_image_update', [
            'projectId' => $projetId,
            'user'      => $this->getUser()->getId(),
        ]);

        if (!$uploadedFile instanceof UploadedFile) {
            return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }

        $projet = $em->getRepository(Projets::class)->find($projetId);
        if (!$projet) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        // Récupère ou crée la Photo existante
        $photo = $projet->getCoverPhoto();
        if (!$photo) {
            $photo = new Photo();
            $photo->setUser($user);
            $projet->setCoverPhoto($photo);
            $em->persist($photo);
        } else {
            // Supprime l'ancien fichier via Vich
            $mapping = $this->propertyMappingFactory->fromField($photo, 'imageFile');
        
            // Extrait juste le nom de fichier pour le logger
            $oldPath     = $photo->getPath();                            // ex. "uploads/photos/14/ancien.png"
            $oldFilename = basename($oldPath);                           // ex. "ancien.png"
        
            $this->storage->remove($photo, $mapping);
            $logger->info('🔥 Ancien cover supprimé', [
                'oldFile' => $oldFilename,
            ]);
        }

        // On persiste explicitement la photo au cas où
        $photo->setImageFile($uploadedFile);
        $photo->setAlt('Cover image for project ' . $projet->getTitle());
        $photo->setDescription('Cover image uploaded for project');
        $em->persist($photo);

        // On persiste aussi le projet pour la relation OneToOne
        $em->persist($projet);
        $em->flush();

        $fileUrl = $this->storage->resolveUri($photo, 'imageFile');
        return new JsonResponse(['url' => $fileUrl]);
    }

}
