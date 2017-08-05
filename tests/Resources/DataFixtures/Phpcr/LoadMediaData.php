<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\DataFixtures\Phpcr;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PHPCR\Util\NodeHelper;
use Doctrine\ODM\PHPCR\Document\Generic;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;
use Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content;

class LoadMediaData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        NodeHelper::createPath($manager->getPhpcrSession(), '/test');

        $testDataDir = realpath(__DIR__.'/../../app/Resources/data');

        $root = $manager->find(null, '/test');

        // media root
        $mediaRoot = new Generic();
        $mediaRoot->setNodename('media');
        $mediaRoot->setParentDocument($root);
        $manager->persist($mediaRoot);

        // content root
        $contentRoot = new Generic();
        $contentRoot->setNodename('content');
        $contentRoot->setParentDocument($root);
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
        $image->setFileContentFromFilesystem($testDataDir.'/cmf-logo.png');
        $manager->persist($image);

        $image2 = new Image();
        $image2->setParent($contentRoot);
        $image2->setName('cmf-logo-2.png');
        $image2->setFileContentFromFilesystem($testDataDir.'/cmf-logo.png');
        $manager->persist($image2);

        // Content with image
        $content = new Content();
        $content->setParent($contentRoot);
        $content->setName('content-with-image');
        $content->setTitle('Content document with image embedded');

        $contentImage = new Image();
        $contentImage->setFileContentFromFilesystem($testDataDir.'/cmf-logo.png');

        $content->setFile($contentImage);
        $manager->persist($content);

        // Content with file
        $content2 = new Content();
        $content2->setParent($contentRoot);
        $content2->setName('content-with-file');
        $content2->setTitle('Content document with file attached');

        $contentFile = new File();
        $contentFile->setFileContentFromFilesystem($testDataDir.'/testfile.txt');

        $content2->setFile($contentFile);
        $manager->persist($content2);

        $manager->flush();
    }
}
