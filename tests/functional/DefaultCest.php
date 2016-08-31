<?php

//use \FunctionalTester;

class DefaultCest
{
    public function tryToGetHomePage(FunctionalTester $I)
    {
        $I->wantTo('Ensure that front page works');
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
    }
}
