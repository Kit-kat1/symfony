<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 5:48 PM
 */
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('label' => 'Username', 'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('email', 'email', array('label' => 'Email', 'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('firstName', 'text', array('label' => 'First name', 'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('lastName', 'text', array('label' => 'Last name', 'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('phoneNumber', 'text', array('label' => 'PhoneNumber', 'required' => false,
                'attr' => array('class' => 'form-control phoneNumber'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('roles', 'collection', array('type' => 'text', 'label' => 'Roles', 'options'  => array(
                'label' => false, 'required' => false, 'attr' => array('class' => 'tokenfield')),
                'label_attr' => array('class' => 'formLabel')))
            ->add('enabled', 'checkbox', array('label' => 'Enabled ', 'required'  => false,
                'label_attr' => array('class' => 'formLabel')))
            ->add('password', 'text', array('label' => 'Password', 'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'formLabel')))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Users',
            'csrf_protection'   => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'users';
    }
}