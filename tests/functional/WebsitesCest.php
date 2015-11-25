<?php

use FOS\RestBundle\Util\Codes;
use Codeception\Util\Stub;
use \AppTester;

class WebsitesCest
{
    public function _before(AppTester $I)
    {
        $this->getModule('\Symfony2')->kernel;
    }

    public function _after(AppTester $I)
    {
    }

    public function createWebsite(AppTester $I)
    {
//        $owner =
//
//        $I->persistEntity(new AppBundle\Entity\Websites, array('name' => 'New site', 'url' => 'sitename.com',
//            'status' => 'up', 'owner' => $owner));
    }

    public function tryCreateWebsiteSuccess(AppTester $I)
    {
        $I->wantTo('Create website');
        $I->amHttpAuthenticated('user', 'qwerty');
        Stub::constructEmptyExcept(
            '\AppBundle\Util\CreateCheck',
            'create',
            array('em' => $I->grabServiceFromContainer('service_container')),
            array('create' => function () {
                return array('id' => 214358, 'name' => 'Demo site');
            })
        );
        $I->amOnPage('/profile/website/create');
        $I->see('Create website');
        $I->fillField('websites[name]', 'Demo site');
        $I->fillField('websites[url]', 'site.com');
        $I->selectOption('select', 'up');
        $I->click('button', '#createWebsite');
        $I->seeResponseCodeIs(Codes::HTTP_OK);
    }

    public function tryCreateWebsiteFailed(AppTester $I)
    {

        $I->wantTo('Create website with invalid params');
        $I->amHttpAuthenticated('user', 'qwerty');
        $I->amOnPage('/profile/website/create');
        $I->see('Create website');
        $I->fillField('websites[name]', 'demo');
        $I->fillField('websites[url]', 'site.com');
        $I->selectOption('select', 'up');
        $I->click('button', '#createWebsite');

        $I->see('This value is already used.');
    }

    public function tryDeleteWebsite(AppTester $I)
    {
        $I->wantTo('Delete website');
        $I->amHttpAuthenticated('user', 'qwerty');
        $I->amOnPage('/profile');
        $this->createWebsite($I);
        $id = $I->grabFromRepository('\AppBundle\Entity\Websites', 'id', array('name' => 'New site'));
        $I->sendAjaxRequest('DELETE', '/profile/website/delete/' . $id);

        $I->dontSeeInRepository('\AppBundle\Entity\Websites', array('name' => 'New site'));
//        $I->seeResponseCodeIs(Codes::HTTP_OK);
    }
}
