<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Functional\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class ImageTest extends BaseTestCase
{
    protected $dm;

    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\DataFixtures\Phpcr\LoadMediaData',
        ));
        $this->dm = $this->db('PHPCR')->getOm();
    }

    public function testPersistence()
    {
        $contentWithFile = $this->dm->find(null, '/test/content/content-with-file');
        $this->assertInstanceOf('Doctrine\ODM\PHPCR\Document\Resource', $contentWithFile->getFile()->getContent());
        $this->assertEquals('This is a test file used to test uploads.', $contentWithFile->getFile()->getContentAsString());

        $testContent = 'Test file content. Changed now.';
        $stream = fopen('php://memory', 'rwb+');
        fwrite($stream, $testContent);
        rewind($stream);

        $contentWithFile->getFile()->setContentFromStream($stream);
        $this->dm->persist($contentWithFile);
        $this->dm->flush();
        $this->dm->clear();

        $contentWithFile = $this->dm->find(null, '/test/content/content-with-file');
        $this->assertInstanceOf('Doctrine\ODM\PHPCR\Document\Resource', $contentWithFile->getFile()->getContent());
        $this->assertEquals('Test file content. Changed now.', $contentWithFile->getFile()->getContentAsString());
    }
}
