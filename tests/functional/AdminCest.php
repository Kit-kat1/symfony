<?php


class AdminCest
{
    /**
     * @param AppTester $I
     */
    public function tryToGoOnAdminPageRedirects(AppTester $I)
    {
        $I->wantTo('Redirect me on login page');
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/login');
    }

    /**
     * @param AppTester $I
     */
    public function tryToGoOnAdminPageDenied(AppTester $I)
    {
        $I->wantTo('Go on page admin and ensure that access will be denied');
        $I->amHttpAuthenticated('user', 'qwerty');
        $I->amOnPage('/admin');
        $I->see('Access Denied');
    }

    /**
     * @param AppTester $I
     */
    public function tryToGoOnAdminSuccess(AppTester $I)
    {
        $I->wantTo('Go on page admin');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/admin');
        $I->see('Admin');
    }
}