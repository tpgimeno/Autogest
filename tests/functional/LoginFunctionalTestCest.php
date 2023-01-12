<?php

namespace Tests\functional;

use FunctionalTester;

class LoginFunctionalTestCest
{
    public function _before(FunctionalTester $I)
    {        
    }
    // tests
    public function LoginFailTest(FunctionalTester $I) {
        $I->amGoingTo("Verificar el mensaje de error al fallar en el login");
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tony@hotmail.com', 'password' => '435665']);
        $I->see('El usuario o el Password no es correcto');
    }

    public static function LoginPassTest(FunctionalTester $I) {
        $I->amGoingTo("Comprobar que el login efectua correctamente");
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']); 
        $I->amGoingTo("Valido la página en que me encuentro");
        $I->canSee("Mantenimiento");        
    }
}
