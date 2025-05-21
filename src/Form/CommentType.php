<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your name']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Your name should be at least {{ limit }} characters',
                        'maxMessage' => 'Your name cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Your name',
                ],
                'label' => 'Name',
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Email(['message' => 'Please enter a valid email address']),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'your.email@example.com (optional)',
                ],
                'label' => 'Email (Optional)',
                'help' => 'We will not share your email address',
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your comment']),
                    new Length([
                        'min' => 5,
                        'max' => 2000,
                        'minMessage' => 'Your comment should be at least {{ limit }} characters',
                        'maxMessage' => 'Your comment cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write your comment here...',
                    'rows' => 5,
                ],
                'label' => 'Comment',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
