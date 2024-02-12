<?php


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class loginCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('P4_social_network/login.php');
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->waitForElement(['name' => 'username']);
        $I->fillField('username', 'timc6t');
        $I->fillField('password', 'password');
        $I->click('Login');
    }
}
