<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Unit\Doctrine;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\Document\File;
use org\bovigo\vfs\vfsStream;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\DoctrineStreamRewindSubscriber;

class DoctrineFileStreamTest extends \PHPUnit_Framework_TestCase
{
    public function resetStreamProvider()
    {
        // image example from http://php.net/manual/en/function.imagecreatefromstring.php
        $pngImage = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            .'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            .'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            .'8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        $pngImage = base64_decode($pngImage);

        return array(
            array(''),
            array($pngImage),
        );
    }

    /**
     * @dataProvider resetStreamProvider
     */
    public function testResetStream($content)
    {
        // create image
        vfsStream::setup('home');
        $fileSystemFile = vfsStream::url('home/test.png');
        file_put_contents($fileSystemFile, $content);

        // test for each implemented persistence layer
        foreach (array(
            'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image',
        ) as $class) {
            $image = new $class();
            $image->setFileContentFromFilesystem($fileSystemFile);

            if ($image instanceof File) {
                $stream = $image->getContent()->getData();
                stream_get_contents($stream); // puts cursor at the end
            }

            $lifecycleEventArgsMock = $this->createMock(LifecycleEventArgs::class);
            $lifecycleEventArgsMock->expects($this->once())
                ->method('getObject')
                ->will($this->returnValue($image))
            ;

            $subscriber = new DoctrineStreamRewindSubscriber();
            $subscriber->rewindStream($lifecycleEventArgsMock);

            if ($image instanceof File) {
                $stream = $image->getContent()->getData();

                if (empty($content)) {
                    $this->assertEquals(0, ftell($stream));
                } else {
                    $this->assertFalse(feof($stream));
                }
            }
        }
    }
}
