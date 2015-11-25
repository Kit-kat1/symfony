<?php

use Codeception\Util\Stub;

class WebsiteTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testCreateWebsiteSuccess(AppTester $I)
    {
        $I->wantTo('Create website');
        $I->amHttpAuthenticated('user', 'qwerty');
        $mock = Stub::constructEmptyExcept(
            '\AppBundle\Util\CreateCheck',
            'create',
            array('em' => $I->grabServiceFromContainer('service_container')),
            array('create' => function () {
                return array('id' => 214358, 'name' => 'Demo site');
            })
        );
        $this->getModule('Symfony2')->kernel->getContaoner()->set('app.pingdom_create_new_check', $mock);
        $I->amOnPage('/profile/website/create');
        $I->see('Create website');
        $I->fillField('websites[name]', 'Demo site');
        $I->fillField('websites[url]', 'site.com');
        $I->selectOption('select', 'up');
        $I->click('button', '#createWebsite');
        $I->seeResponseCodeIs(Codes::HTTP_OK);
    }

}