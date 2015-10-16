<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Unit\Templating\Helper;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Bundle\MediaBundle\Templating\Helper\CmfMediaHelper;

class CmfMediaHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testDownloadUrl()
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('http://www.example.com/media/download/test/media/file-download'))
        ;

        $mediaManager = $this->getMock('Symfony\Cmf\Bundle\MediaBundle\MediaManagerInterface');
        $mediaManager->expects($this->once())
            ->method('getUrlSafePath')
            ->will($this->returnValue('test/media/file-download'))
        ;

        $file = new File();
        $file->setName('file-download');
        $file->setId('/test/media/file-download');
        $file->setContentFromString('File download url test.');

        $mediaHelper = new CmfMediaHelper($mediaManager, $router);
        $mediaHelper->downloadUrl($file);
    }
}
