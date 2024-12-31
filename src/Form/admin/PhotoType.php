<?php

namespace App\Form\admin;

use App\Entity\Photo;
use App\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alt')
            ->add('description')
            ->add('imageFile', FileType::class, [
                'required' => false,
                'label' => 'Image',
            ])
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'id',
                'placeholder' => 'Sélectionnez une section', // Ajoute un choix vide
                'required' => false, // Rend le champ facultatif
            ])
            ->add('save', SubmitType::class);
            // ->add('size')
            // ->add('mimeType')
            // ->add('path')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
