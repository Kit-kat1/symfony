<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class WebsitesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Name', 'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('url', 'url', array('label' => 'Url',
                'attr' => array('class' => 'form-control', 'placeholder' => 'Like http://your_site.com'),
                'label_attr' => array('class' => 'formLabel')))
            ->add('status', 'choice', array('attr' => array('class' => 'choice'),
                'choices' => array('up' => 'Up', 'down' => 'Down')))
            ->add('owner', 'entity', array('class' => 'AppBundle\Entity\Users', 'label' => ' ',
                'attr' => array('hidden' => true,'class' => 'choice')));

        $builder->get('url')
            ->addModelTransformer(new CallbackTransformer(
                function ($originalDescription) {
                    return $originalDescription;
                },
                function ($submittedDescription) {
                    return substr($submittedDescription, 7);
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Websites',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'websites';
    }
}
