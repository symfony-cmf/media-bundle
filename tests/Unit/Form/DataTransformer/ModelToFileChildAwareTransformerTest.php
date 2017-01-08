<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Unit\Form\DataTransformer;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer\ModelToFileChildAwareTransformer;
use Symfony\Cmf\Bundle\MediaBundle\Form\DataTransformer\ModelToFileTransformer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class ModelToFileChildAwareTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UploadFileHelperInterface
     */
    private $uploadFileHelper;

    public function setUp()
    {
        $this->uploadFileHelper = $this->getMock(UploadFileHelperInterface::class);
    }

    public function testTransformPassFileOnly()
    {
        $transformer = new ModelToFileTransformer($this->uploadFileHelper, []);
        $file = new \stdClass();

        $result = $transformer->transform($file);

        $this->assertEquals($file, $result);
    }

    public function testReverseTransformPassNonUploadFiles()
    {
        $transformer = new ModelToFileTransformer($this->uploadFileHelper, []);
        $file = new \stdClass();

        $result = $transformer->reverseTransform($file);

        $this->assertEquals($file, $result);
    }

    public function testReverseTransformReturnHandlesFile()
    {
        $file = new File();
        $uploadFile = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock();
        $this->uploadFileHelper
            ->expects($this->once())
            ->method('handleUploadedFile')
            ->with($this->equalTo($uploadFile), $this->equalTo('Some\\Class'))
            ->will($this->returnValue($file));
        $transformer = new ModelToFileChildAwareTransformer($this->uploadFileHelper, 'Some\\Class');
        $result = $transformer->reverseTransform($uploadFile);

        $this->assertEquals($file, $result);
    }

    public function testRespectOfEmptyDataFile()
    {
        $uploadFile = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock();
        $createdFile = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $emptyDataFile = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();

        $this->uploadFileHelper
            ->expects($this->once())
            ->method('handleUploadedFile')
            ->with($this->equalTo($uploadFile), $this->equalTo('Some\\Class'))
            ->will($this->returnValue($createdFile));

        $createdFile->expects($this->once())->method('getContentAsStream')->will($this->returnValue('some-stream'));
        $emptyDataFile->expects($this->once())->method('setContentFromStream')->with($this->equalTo('some-stream'));
        $emptyDataFile->expects($this->once())->method('setName')->with($this->equalTo('someNodeName'));

        $transformer = new ModelToFileChildAwareTransformer(
            $this->uploadFileHelper,
            'Some\\Class',
            $emptyDataFile,
            'someNodeName'
        );

        $result = $transformer->reverseTransform($uploadFile);

        $this->assertEquals($emptyDataFile, $result);
    }
}
