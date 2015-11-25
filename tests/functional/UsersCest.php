<?php

use \AppTester;
use FOS\RestBundle\Util\Codes;
use AppBundle\Entity\Users;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UsersCest
{
    private $container;

    public function _before(AppTester $I)
    {
    }

    public function _after(AppTester $I)
    {
    }

    public function tryToCreateUserSuccess(AppTester $I)
    {
        $I->wantTo('Create user');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin/user/create');
        $I->fillField('users[username]', 'john');
        $I->fillField('users[email]', 'john@doe.com');
        $I->fillField('users[firstName]', 'User');
        $I->fillField('users[lastName]', 'Surname');
        $I->fillField('users[phoneNumber]', '0937485029');
        $I->fillField('users[roles][0]', 'ROLE_SUPER_ADMIN, ROLE_USER');
        $I->checkOption('users[enabled]');
        $I->fillField('users[password]', 'qwerty');
        $I->click('button', '#saveUser');

        $I->seeInRepository(
            'AppBundle:Users',
            [
                'username' => 'john',
            ]
        );
        $I->seeResponseCodeIs(Codes::HTTP_OK);
    }

    public function createUser(AppTester $I)
    {
        $I->persistEntity(new AppBundle\Entity\Users, array('username' => 'john', 'email' => 'john@doe.com',
            'firstName' => 'User', 'lastName' => 'Surname', 'phoneNumber' => '0955845738', 'roles' =>
                array(0 => 'ROLE_SUPER_ADMIN, ROLE_USER'), 'enabled' => true, 'password' => 'qwerty'));
    }

    public function tryToCreateUserFailed(AppTester $I)
    {
        $I->wantTo('Create user and see exception "This value is already used." ');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin/user/create');
        $I->fillField('users[username]', 'user1');
        $I->fillField('users[email]', 'john@mail.ru');
        $I->fillField('users[firstName]', 'User');
        $I->fillField('users[lastName]', 'Surname');
        $I->fillField('users[phoneNumber]', '0937485029');
        $I->fillField('users[roles][0]', 'ROLE_SUPER_ADMIN, ROLE_USER');
        $I->checkOption('users[enabled]');
        $I->fillField('users[password]', 'qwerty');
        $I->click('button', '#saveUser');

        $I->see('This value is already used.');
    }

    public function tryToDeleteUser(AppTester $I)
    {
        $I->wantTo('Delete user');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin');
        $this->createUser($I);
        $id = $I->grabFromRepository('\AppBundle\Entity\Users', 'id', array('username' => 'john'));
        $I->sendAjaxRequest('DELETE', '/admin/user/delete/' . $id);

        $I->dontSeeInRepository('\AppBundle\Entity\Users', array('username' => 'john'));
        $I->seeResponseCodeIs(Codes::HTTP_OK);
    }

    public function tryToDeleteUserFailed(AppTester $I)
    {
        $I->wantTo('Try delete user and see response code 404');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin');

        $I->sendAjaxRequest('DELETE', '/admin/user/delete/1000000');

        $I->seeResponseCodeIs(Codes::HTTP_NOT_FOUND);
    }

    public function tryToUpdateUser(AppTester $I)
    {
        $I->wantTo('Update users data');
        $I->amHttpAuthenticated('admin', 'admin');
        $this->createUser($I);
        $id = $I->grabFromRepository('\AppBundle\Entity\Users', 'id', array('username' => 'john'));
        $I->amOnPage('/admin/user/edit/' . $id);
        $I->see('Edit user');
        $I->fillField('users[username]', 'userName');
        $I->fillField('users[email]', 'jonny@mail.ru');
        $I->fillField('users[firstName]', 'User');
        $I->fillField('users[lastName]', 'Surname');
        $I->fillField('users[phoneNumber]', '0937485029');
        $I->fillField('users[roles][0]', 'ROLE_SUPER_ADMIN, ROLE_USER');
        $I->checkOption('users[enabled]');
        $I->click('button', '#saveUser');

        $I->seeInRepository('\AppBundle\Entity\Users', array('username' => 'userName'));
        $I->see('Admin');
        $I->seeResponseCodeIs(Codes::HTTP_OK);
    }

    public function tryToUpdateUserFailed(AppTester $I)
    {
        $I->wantTo('Update users data');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin/user/edit/100000');
        $I->see('There is no user with id = 100000');
        $I->dontSee('Edit user');
    }
}
