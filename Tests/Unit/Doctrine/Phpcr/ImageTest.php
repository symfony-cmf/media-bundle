<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Unit\Doctrine\Phpcr;

use org\bovigo\vfs\vfsStream;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function updateDimensionsFromContentProvider()
    {
        // image example from http://php.net/manual/en/function.imagecreatefromstring.php
        $pngImage = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        $pngImage = base64_decode($pngImage);

        return array(
            array(''),
            array($pngImage),
        );
    }

    /**
     * @dataProvider updateDimensionsFromContentProvider
     */
    public function testUpdateDimensionsFromContent($content)
    {
        // create image
        vfsStream::setup('home');
        $fileSystemFile = vfsStream::url('home/test.png');
        file_put_contents($fileSystemFile, $content);

        // determine expected values
        $fileSize = filesize($fileSystemFile);

        if ($fileSize) {
            $size = getimagesize($fileSystemFile);
            $expectedWidth       = $size[0];
            $expectedHeight      = $size[1];
            $expectedContentType = $size['mime'];
            $expectedSize        = $fileSize;
        } else {
            $expectedWidth       = 0;
            $expectedHeight      = 0;
            $expectedContentType = 'inode/x-empty';
            $expectedSize        = 0;
        }

        $image = new Image();

        $image->setFileContentFromFilesystem($fileSystemFile);

        $this->assertEquals($expectedContentType, $image->getContentType());
        $this->assertEquals($expectedSize, $image->getSize());
        $this->assertInstanceOf('DateTime', $image->getUpdatedAt());
        $this->assertEquals($expectedWidth, $image->getWidth());
        $this->assertEquals($expectedHeight, $image->getHeight());
    }
}