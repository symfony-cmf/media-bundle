<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelper;
use Symfony\Component\HttpFoundation\Request;

class PhpcrFileTestController extends Controller
{
    public function getUploadForm($editor = null)
    {
        if ($editor) {
            $action = $this->generateUrl('cmf_media_file_upload', array('editor' => $editor));
        } else {
            $action = $this->generateUrl('phpcr_file_test_upload');
        }

        return $this->container->get('form.factory')->createNamedBuilder(null, 'form', null, array('action' => $action))
            ->add('file', 'file')
            ->add('submit', 'submit')
            ->getForm()
        ;
    }

    public function fileAction(Request $request)
    {
        $fileClass = 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File';
        $dm        = $this->get('doctrine_phpcr')->getManager('default');
        $files     = $dm->getRepository($fileClass)->findAll();

        $uploadForm = $this->getUploadForm();
        $editorUploadForm = $this->getUploadForm('default');

        return $this->render('::tests/file.html.twig', array(
            'upload_form'  => $uploadForm->createView(),
            'editor_form'  => $editorUploadForm->createView(),
            'files' => $files,
        ));
    }

    public function uploadAction(Request $request)
    {
        $form = $this->getUploadForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var UploadFileHelper $uploadFileHelper */
            $uploadFileHelper = $this->get('cmf_media.upload_file_helper');

            $uploadedFile = $request->files->get('file');

            $file = $uploadFileHelper->handleUploadedFile($uploadedFile);

            // persist
            $dm = $this->get('doctrine_phpcr')->getManager('default');
            $dm->persist($file);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('phpcr_file_test'));
    }
}
