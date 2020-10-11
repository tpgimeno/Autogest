<?php 

class FirstCest
{
    public function frontpageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/intranet');
        $I->see('Automotive');
    }
}