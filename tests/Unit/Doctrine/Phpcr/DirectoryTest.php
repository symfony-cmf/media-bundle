<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Unit\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    public function testAddChild()
    {
        $dir = new Directory();
        $dir->setName('home');

        $subDir = new Directory();
        $subDir->setName('subdir');

        $dir->addChild($subDir);

        $this->assertNull($dir->getParent());
        $this->assertNull($subDir->getParent());
        $this->assertCount(1, $dir->getChildren());
        $this->assertCount(0, $subDir->getChildren());
    }

    public function testSetParent()
    {
        $dir = new Directory();
        $dir->setName('home');

        $subDir = new Directory();
        $subDir->setName('subdir');

        $subDir->setParent($dir);

        $this->assertNull($dir->getParent());
        $this->assertEquals($dir, $subDir->getParent());
        $this->assertCount(1, $dir->getChildren());
        $this->assertCount(0, $subDir->getChildren());
    }

    public function addChildProvider()
    {
        return array(
            array('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Media'),
            array('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File'),
            array('Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image'),
        );
    }

    /**
     * @dataProvider addChildProvider
     */
    public function testChild($class)
    {
        $dir = new Directory();
        $dir->setName('home');

        $media = new $class();
        $media->setName('media');

        $media->setParent($dir);

        $this->assertNull($dir->getParent());
        $this->assertEquals($dir, $media->getParent());
        $this->assertCount(1, $dir->getChildren());
        $this->assertEquals($media, $dir->getChildren()->first());
    }
}
