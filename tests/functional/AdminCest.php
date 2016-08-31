<?php

//use \FunctionalTester;

class AdminCest
{
    public function tryToGoOnAdminPageRedirects(FunctionalTester $I)
    {
        $I->wantTo('Redirect me on login page');
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/login');
    }

    public function tryToGoOnAdminPageDenied(FunctionalTester $I)
    {
        $I->wantTo('Go on page admin and ensure that access will be denied');
        $I->amHttpAuthenticated('user', 'qwerty');
        $I->amOnPage('/admin');
        $I->see('Access Denied');
    }

    public function tryToGoOnAdminSuccess(FunctionalTester $I)
    {
        $I->wantTo('Go on page admin');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin');
        $I->see('Admin');
    }
}