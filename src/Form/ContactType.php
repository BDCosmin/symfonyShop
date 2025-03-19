<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $username = $options['current_user'] instanceof UserInterface ? $options['current_user']->getUsername() : 'You have to be logged in to write a feedback';

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'data' => $username,
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Your Feedback',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Write your feedback here...',
                    'rows' => 5,
                    'maxlength' => 1000,
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please provide your feedback.',
                    ]),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'Your feedback is too long. Maximum {{ limit }} characters allowed.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send Feedback',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_user' => null
        ]);
    }
}
