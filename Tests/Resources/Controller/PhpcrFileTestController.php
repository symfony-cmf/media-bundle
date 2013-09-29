<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Component\HttpFoundation\Request;

class PhpcrFileTestController extends Controller
{
    public function getUploadForm()
    {
        return $this->container->get('form.factory')->createNamedBuilder(null, 'form')
            ->add('file', 'file')
            ->getForm()
        ;
    }

    public function indexAction(Request $request)
    {
        $fileClass = 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File';
        $dm        = $this->get('doctrine_phpcr')->getManager('default');
        $files     = $dm->getRepository($fileClass)->findAll();

        $uploadForm = $this->getUploadForm();
        $editorUploadForm = $this->getUploadForm();

        return $this->render('::tests/file.html.twig', array(
            'upload_form' => $uploadForm->createView(),
            'editor_form' => $editorUploadForm->createView(),
            'files'       => $files,
        ));
    }

    public function uploadAction(Request $request)
    {
        $form = $this->getUploadForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var UploadFileHelperInterface $uploadFileHelper */
                $uploadFileHelper = $this->get('cmf_media.upload_file_helper');

                $uploadedFile = $request->files->get('file');

                $file = $uploadFileHelper->handleUploadedFile($uploadedFile);

                // persist
                $dm = $this->get('doctrine_phpcr')->getManager('default');
                $dm->persist($file);
                $dm->flush();
            }
        }

        return $this->redirect($this->generateUrl('phpcr_file_test'));
    }
}
