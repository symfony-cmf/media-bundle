<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\Document\Generic;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;
use Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content;

class LoadMediaData implements FixtureInterface, DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            'Symfony\Cmf\Component\Testing\DataFixtures\PHPCR\LoadBaseData',
        );
    }

    public function load(ObjectManager $manager)
    {
        $testDataDir = realpath(__DIR__ . '/../../app/Resources/data');

        $root = $manager->find(null, '/test');

        // media root
        $mediaRoot = new Generic();
        $mediaRoot->setNodename('media');
        $mediaRoot->setParent($root);
        $manager->persist($mediaRoot);

        // content root
        $contentRoot = new Generic();
        $contentRoot->setNodename('content');
        $contentRoot->setParent($root);
        $manager->persist($contentRoot);

        // File
        $file = new File();
        $file->setParent($mediaRoot);
        $file->setName('file-1.txt');
        $file->setContentFromString('Test file 1.');
        $file->setContentType('text/plain');
        $manager->persist($file);

        // Image
        $image = new Image();
        $image->setParent($mediaRoot);
        $image->setName('cmf-logo.png');
        $image->setFileContentFromFilesystem($testDataDir .'/cmf-logo.png');
        $manager->persist($image);

        $image2 = new Image();
        $image2->setParent($contentRoot);
        $image2->setName('cmf-logo-2.png');
        $image2->setFileContentFromFilesystem($testDataDir .'/cmf-logo.png');
        $manager->persist($image2);

        // Content
        $content = new Content();
        $content->setParent($contentRoot);
        $content->setName('content-with-image');
        $content->setTitle('Content document with image embedded');

        $contentImage = new Image();
        $contentImage->setName('cmf-logo.png');
        $contentImage->setFileContentFromFilesystem($testDataDir .'/cmf-logo.png');

        $content->setImage($contentImage);
        $manager->persist($content);

        $manager->flush();
    }
}
