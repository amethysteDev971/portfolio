<?php

namespace App\Form\admin;

use App\Entity\Photo;
use App\Entity\Projets;
use App\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SectionByProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champs pour Section
            // ->add('range_position', IntegerType::class, [
            //     'label' => 'Position',
            // ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            // Champs pour Photo
            ->add('alt', TextType::class, [
                'label' => 'Texte alternatif',
                'mapped' => false, // Ce champ n'est pas directement lié à Section
            ])
            ->add('description_photo', TextareaType::class, [
                'label' => 'Description de la photo',
                'mapped' => false, // Ce champ n'est pas directement lié à Section
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/x-png',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpeg, png, gif).',
                    ])
                ],
            ])
             ->add('save', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
