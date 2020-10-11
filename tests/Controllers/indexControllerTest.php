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
   final public function createKernel(array $options = array()) {
       parent::createKernel($options);
   }
   public function testIndex()
   {
       $client = static::createClient();
       $client->request('GET', '/intranet');
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }
}
 