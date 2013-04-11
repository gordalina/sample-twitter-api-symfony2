<?php

namespace Twitter\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TweetControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tweets/{id}');
    }

    public function testPost()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tweets/{id}');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tweets/{id}');
    }

}
