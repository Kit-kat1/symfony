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
use AppBundle\Entity\Websites;
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
        $formData = array(
            'name' => 'Some site',
            'url' => 'awesome.com',
            'status' => 'up',
            'owner' => 136
        );

        $website = new Websites();
        $form = $this->factory->create(new WebsitesType(), $website);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}