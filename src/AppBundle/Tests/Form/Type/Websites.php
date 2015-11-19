<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/19/15
 * Time: 12:53 PM
 */

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\UsersType;
use AppBundle\Form\WebsitesType;
use Symfony\Component\Form\Forms;
use AppBundle\Entity\Users;

use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WebsitesTypeTest extends TypeTestCase
{
    //Test checks that none of data transformers used by the form failed.
    //Checks the creation of the FormView (all widgets that displayed are available in the children property:
    public function testSubmittedData()
    {
//        $object = new Users();
//        $object->setUpdated();
//        $object->setUsername('user');
//        $object->setFirstName('John');
//        $object->setLastName('Smith');
//        $object->setSalt(md5(time()));
//        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
//        $object->setPassword($encoder->encodePassword($object->getPassword(), $object->getSalt()));
//        $object->setEmail('john@mail.ru');
//        $object->setPhoneNumber('1');
//        $object->setPhoneNumber('0947854623');

        $formData = new WebsitesType();
        $formData = array(
            'name' => 'katy@mail.ru',
            'url' => 'User',
            'status' => 'up',
            'owner' => 138
        );

        $type = new WebsitesType();
//        var_dump($type);die();
        $form = $this->factory->create($type);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->getFormFactory();
    }

    protected function getExtensions()
    {
        
    }
}