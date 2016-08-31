<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/18/15
 * Time: 6:24 PM
 */
namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\UsersType;
use AppBundle\Entity\Users;
use Symfony\Component\Form\Test\TypeTestCase;

class UsersTypeTest extends TypeTestCase
{
    //Test checks that none of data transformers used by the form failed.
    //Checks the creation of the FormView (all widgets that displayed are available in the children property:
    public function testSubmittedData()
    {
        $user = new Users();

        $form = $this->factory->create(new UsersType(), $user);

        $formData = array(
            'username' => 'user',
            'email' => 'john@doe.com',
            'firstName' => 'User',
            'lastName' => 'Surname',
            'phoneNumber' => '0955845738',
            'roles' => array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER'),
            'enabled' => '1',
            'password' => 'qwerty'
        );

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());
        $this->assertEquals('user', $user->getUsername());
        $this->assertEquals('john@doe.com', $user->getEmail());
        $this->assertEquals('User', $user->getFirstName());
        $this->assertEquals('Surname', $user->getLastName());
        $this->assertEquals('0955845738', $user->getPhoneNumber());

        $this->assertTrue($form->isValid());

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}