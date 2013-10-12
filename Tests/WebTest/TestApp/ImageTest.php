<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\WebTest\TestApp;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageTest extends BaseTestCase
{
    private $testDataDir;

    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\DataFixtures\Phpcr\LoadMediaData',
        ));
        $this->testDataDir = $this->getContainer()->get('kernel')->getRootDir() . '/Resources/data';
    }

    public function testPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $resp = $client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());

        // image(s) display
        $this->assertGreaterThanOrEqual(4, $crawler->filter('.images li img')->count());

        // cmf_media_image form tests
        $this->assertEquals(0, $crawler->filter('.cmf_media_image.new img')->count());
        $this->assertEquals(1, $crawler->filter('.cmf_media_image.edit.default img')->count());
        $this->assertEquals(1, $crawler->filter('.cmf_media_image.edit.imagine img')->count());

        // cmf_media_display_url
        $defaultImageLink = $crawler->filter('.images li img.default')->first()->attr('src');
        $client->request('get', $defaultImageLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'default image test');

        // imagine_filter
        $this->getContainer()->get('liip_imagine.cache.clearer')->clear('image_upload_thumb');
        $imagineImageLink = $crawler->filter('.images li img.imagine')->first()->attr('src');
        $client->request('get', $imagineImageLink);
        $this->assertTrue($client->getResponse()->isSuccessful(), 'imagine image test');
    }

    public function testUpload()
    {
        $client = $this->createClient();
        $crawler = $client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $cntImagesLinks = $crawler->filter('.images li img')->count();

        $buttonCrawlerNode = $crawler->filter('form.standard')->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['image']->upload($this->testDataDir . '/testimage.png');

        $client->submit($form);
        $crawler = $client->followRedirect();
        $resp = $client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($cntImagesLinks + 2, $crawler->filter('.images li img')->count());
    }

    public function testEditorUpload()
    {
        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'adminpass',
        ));
        $crawler = $client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $cntImagesLinks = $crawler->filter('.images li img')->count();

        $buttonCrawlerNode = $crawler->filter('form.editor.default')->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['image']->upload($this->testDataDir . '/testimage.png');

        $client->submit($form);
        $crawler = $client->followRedirect();
        /** @var Response $resp */
        $resp = $client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        // check that the content is not empty, this could be caused by the stream cursor that is not at the beginning
        // when doctrine persist a file object
        $this->assertNotEmpty($resp->getContent());
        $this->assertEquals('image/png', $resp->headers->get('Content-Type')); // check that the response is an image

        $crawler = $client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $resp = $client->getResponse();
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($cntImagesLinks + 2, $crawler->filter('.images li img')->count());
    }
}
