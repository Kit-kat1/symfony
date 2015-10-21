<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, array('attr' => array('class' => 'form-control', 'placeholder' => 'First name')))
            ->add('lastName', null, array('attr' => array('class' => 'form-control', 'placeholder' => 'Last name')))
            ->add('phoneNumber', 'text', array('attr' => array('class' => 'form-control',
                'placeholder' => 'Phone number')))
            ->add('email', 'email', array('translation_domain' => 'FOSUserBundle',
                'attr' => array('class' => 'form-control', 'placeholder' => 'Email')))
            ->add('username', null, array('translation_domain' => 'FOSUserBundle',
                'attr' => array('class' => 'form-control', 'placeholder' => 'User name')))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'Password','attr' => array('class' => 'form-control',
                    'placeholder' => 'Password')),
                'second_options' => array('label' => 'Repeat password','attr' => array('class' => 'form-control',
                    'placeholder' => 'Repeat password')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
        ;
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
