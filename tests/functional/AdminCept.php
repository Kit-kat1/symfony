<?php

namespace Helper;

class Functional extends \Codeception\Module
{
    public function testAdminRouting()
    {
        $I = new FunctionalTester;
        $I->wantTo('Go on page admin and ensure that access will be denied');
        $I->amOnPage('/admin');
        $I->seeCurrentRouteIs('/login');

        $service = $this->getModule('Symfony2')->grabServiceFromContainer('myservice');
        $service->doSomething();
    }
}