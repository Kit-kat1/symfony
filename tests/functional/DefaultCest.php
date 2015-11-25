<?php

use \AppTester;

class DefaultCest
{
    public function _before(AppTester $I)
    {
    }

    public function _after(AppTester $I)
    {
    }

    public function tryToGetHomePage(AppTester $I)
    {
        $I->wantTo('Ensure that front page works');
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
    }
}
