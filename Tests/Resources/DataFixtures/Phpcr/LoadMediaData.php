<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\Document\Generic;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;

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
        $root = $manager->find(null, '/test');
        $mediaRoot = new Generic;
        $mediaRoot->setNodename('media');
        $mediaRoot->setParent($root);
        $manager->persist($mediaRoot);

        // File
        $file = new File();
        $file->setParent($mediaRoot);
        $file->setName('file-1.txt');
        $file->setContentFromString('Test file 1.');
        $file->setContentType('text/plain');
        $manager->persist($file);

        $manager->flush();
    }
}
