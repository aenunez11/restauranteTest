<?php
/**
 * Created by PhpStorm.
 * User: aenun
 * Date: 24/06/2020
 * Time: 11:16
 */

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebappControllerTest extends WebTestCase
{
    public function testHome()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString('Bienvenido',$client->getResponse()->getContent());

    }
}
