<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\WebTest\TestApp;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class FileTest extends BaseTestCase
{
    public function testPage()
    {
        $client = $this->createClient();
        $client->request('get', $this->getContainer()->get('router')->generate('file_test'));
        $resp = $client->getResponse();
        $this->assertEquals(200, $resp->getStatusCode());
    }
}
