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
/**
 * Defines the form used to create and manipulate blog posts.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UsersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the full reference of options defined by each form field type
        // see http://symfony.com/doc/current/reference/forms/types.html
        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        //
        //     $builder->add('title', null, array('required' => false, ...));
        $builder
            ->add('title', null, array(
                'attr' => array('autofocus' => true),
                'label' => 'label.title',
            ))
            ->add('summary', 'textarea', array('label' => 'label.summary'))
            ->add('content', 'textarea', array(
                'attr' => array('rows' => 20),
                'label' => 'label.content',
            ))
            ->add('authorEmail', 'email', array('label' => 'label.author_email'))
            ->add('publishedAt', 'datetime', array(
                'widget' => 'single_text',
                'label' => 'label.published_at',
            ))
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Users',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_users';
    }
}