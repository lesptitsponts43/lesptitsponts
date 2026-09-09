<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom', 'class' => 'form-control'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom', 'class' => 'form-control'],
            ])
            ->add('tel', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => '+33...', 'class' => 'form-control'],
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'votre@email.com', 'class' => 'form-control'],
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'attr' => ['placeholder' => 'Sujet de votre message', 'class' => 'form-control'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['placeholder' => 'Écrivez votre message ici...', 'class' => 'form-control', 'rows' => 5],
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => '📧 Envoyer mon message',
                'attr' => ['class' => 'btn btn-primary btn-lg mt-3 w-100'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
