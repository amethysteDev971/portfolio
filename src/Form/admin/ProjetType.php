<?php

namespace App\Form\admin;

use App\Entity\Post;
use App\Entity\Projets;
use App\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            // ->add('post', EntityType::class, [
            //     'class' => Post::class,
            //     'choice_label' => 'title',
            // ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'mapped' => false, // Ce champ n'est pas directement lié à Projet
                'required' => false,
                'data' => $options['description'], // Utiliser l'option passée
            ])
            // Champs pour Photo
            ->add('alt', TextType::class, [
                'label' => 'Texte alternatif',
                'mapped' => false, // Ce champ n'est pas directement lié à Section
                'data' => $options['alt'], // Utiliser l'option passée
            ])
            ->add('description_photo', TextareaType::class, [
                'label' => 'Description de la photo',
                'mapped' => false, // Ce champ n'est pas directement lié à Section
                'required' => false,
                'data' => $options['description_photo'], // Utiliser l'option passée
            ])
            ->add('currentImagePath', HiddenType::class, [
                'mapped' => false,
                'data' => $options['image_path'] ?? null, // Option dynamique
            ])
            ->add('imageFile', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,// Facultatif pour ne pas bloquer la création
                'attr'     => ['accept' => 'image/*'],
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/x-png',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpeg, png, gif).',
                    ])
                ],
            ])
            
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
            // Ajoutez ici les options personnalisées
            'alt' => null,
            'description' => null,
            'description_photo' => null,
            'image_path' => null,
        ]);
    }
}
