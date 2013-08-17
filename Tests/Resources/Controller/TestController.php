<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('::index.html.twig');
    }
}
