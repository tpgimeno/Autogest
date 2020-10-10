<?php

namespace App\Tests\Controllers;


/**
 * Description of indexTest
 *
 * @author tonyl
 */

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class indexControllerTest extends WebTestCase 
{
   public function testIndex()
   {
       $client = static::createClient();
       $client->request('GET', '/intranet');
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }
}
 