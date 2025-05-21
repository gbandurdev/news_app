<?php

namespace App\Form;

use App\Entity\News;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a title']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'The title should be at least {{ limit }} characters',
                        'maxMessage' => 'The title cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter news title',
                ],
                'label' => 'News Title',
            ])
            ->add('shortDescription', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a short description']),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'The short description cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter a short description',
                    'rows' => 3,
                ],
                'label' => 'Short Description',
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the content']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the news content',
                    'rows' => 10,
                ],
                'label' => 'Content',
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'News Image',
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-check',
                ],
                'label' => 'Categories',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
