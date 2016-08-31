<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 11/12/15
 * Time: 5:12 PM
 */

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    //Test redirection to login when tries to get admin page without accessing role
    public function testShowAdminPageFailed()
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        $client->followRedirects();
        $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    //Test redirection when tries to get admin page without accessing role
    public function testShowAdminPage()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));

        $client->request('GET', '/admin');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    //Test redirection when tries to get admin page without accessing role
    public function testShowAdminPageFailedPrivilege()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'qwerty',
        ));

        $client->request('GET', '/admin');

        $this->assertEquals($client->getResponse()->isServerError(), 1);
    }
}
