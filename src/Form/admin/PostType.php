<?php
namespace App\Form\admin;

use App\Core\Enum\Type;
use App\Entity\Post;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {

        // type
        $builder
            ->add('title', TextType::class)
            ->add('shortDescription', TextareaType::class,[
                'required' => false,
                'label' => 'Description courte (texte court après le titre)',
            ])
            ->add('longDescription', TextareaType::class, [
                'required' => false,
                'label' => 'Description (description, context du Post)',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Catégories',
                'choices' => array_combine(
                    array_map(fn($case) => $case->value, Type::cases()),
                    Type::cases()
                ),
                'choice_label' => fn(?Type $type) => $type->value,
                'placeholder' => 'Choisir une catégorie...'
            ])
            ->add('save', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

   
}