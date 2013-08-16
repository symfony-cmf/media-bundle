<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    private $fileClass = 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File';

    public function indexAction(Request $request)
    {
        return $this->render('::index.html.twig');
    }

    public function fileAction(Request $request)
    {
        $files = $this->get('doctrine_phpcr')
            ->getRepository($this->fileClass)
            ->findAll();

        return $this->render('::tests/file.html.twig', array(
            'files' => $files,
        ));
    }
}
