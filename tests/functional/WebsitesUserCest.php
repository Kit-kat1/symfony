<?php
use \AppTester;

class WebsitesUserCest
{
    public function _before(AppTester $I)
    {
    }

    public function _after(AppTester $I)
    {
    }

    public function trySaveNotificationCheckedSuccess(AppTester $I)
    {
        $I->wantTo('Save notification about sites');
        $I->amHttpAuthenticated('user', 'qwerty');
        $I->amOnPage('/profile');
        $I->sendAjaxPostRequest(
            '/profile/website/notification/save',
            array('flag' => 0, 'user' => 136, 'website' => array(77,78))
        );
        $I->seeInRepository('AppBundle\Entity\WebsitesUser', array('website' => 78));
    }

    //Checks notification with flag 1. (Drag&Drop panel)
    public function trySaveNotificationSuccess(AppTester $I)
    {
        $I->wantTo('Save notification for users in current site');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/profile/website/edit/77');
        $I->sendAjaxPostRequest(
            '/profile/website/notification/save',
            array('flag' => 1, 'user' => array('admin', 'user'), 'website' => 77)
        );
        $id = $I->grabFromRepository('AppBundle\Entity\Users', 'id', array('username' => 'user'));
        $I->seeInRepository('AppBundle\Entity\WebsitesUser', array('user' => $id));
    }

    public function testSaveNotificationFailed(AppTester $I)
    {
        $I->wantTo('Save notification for users about site. Will return not fount because such site doesn`t exist');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/profile/website/edit/77');
        $I->sendAjaxPostRequest(
            '/profile/website/notification/save',
            array('flag' => 1, 'user' => array(136, 212), 'website' => 23)
        );
        $id = $I->grabFromRepository('AppBundle\Entity\Users', 'id', array('username' => 'user'));
        $I->seeResponseCodeIs(404);
    }

    //Send to method unreal website id. Got response 404 (Not found)
    public function testSaveNotificationCheckedFailed(AppTester $I)
    {
        $I->wantTo('Save notification for users about site. Will return not fount because one site doesn`t exist');
        $I->amHttpAuthenticated('admin', 'admin');
        $I->amOnPage('/profile/website/edit/77');
        $I->sendAjaxPostRequest(
            '/profile/website/notification/save',
            array('flag' => 0, 'user' => 136, 'website' => array(23, 78))
        );
        $id = $I->grabFromRepository('AppBundle\Entity\Users', 'id', array('username' => 'user'));
        $I->seeResponseCodeIs(404);
    }
}
