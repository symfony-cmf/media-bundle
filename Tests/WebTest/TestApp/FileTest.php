<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\WebTest\TestApp;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class FileTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\DataFixtures\Phpcr\LoadMediaData',
        ));
        $this->client = $this->createClient();
    }

    public function testPage()
    {
        $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_file_test'));
        $resp = $this->client->getResponse();
        $this->assertEquals(200, $resp->getStatusCode());
    }
}
