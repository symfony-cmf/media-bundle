<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Controller;

use Doctrine\ODM\PHPCR\Document\Generic;
use PHPCR\Util\PathHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content;
use Symfony\Cmf\Bundle\MediaBundle\Util\LegacyFormHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhpcrFileTestController extends Controller
{
    public function getUploadForm()
    {
        return $this->container->get('form.factory')->createNamedBuilder(null, LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\FormType'))
            ->add('file', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\FileType'))
            ->getForm()
        ;
    }

    protected function getContentForm(Content $contentObject = null)
    {
        $is_new = is_null($contentObject);
        if ($is_new) {
            $contentObject = new Content();
        }

        return $this->createFormBuilder($contentObject)
            ->add('name')
            ->add('title')
            ->add('file', LegacyFormHelper::getType('Symfony\Cmf\Bundle\MediaBundle\Form\Type\FileType'), array('required' => $is_new))
            ->getForm()
            ;
    }

    protected function getUrlSafePath($object)
    {
        return ltrim($object->getId(), '/');
    }

    protected function mapUrlSafePathToId($path)
    {
        // The path is being the id
        return PathHelper::absolutizePath($path, '/');
    }

    public function indexAction(Request $request)
    {
        $fileClass = 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File';
        $dm = $this->get('doctrine_phpcr')->getManager('default');
        $files = $dm->getRepository($fileClass)->findAll();

        $uploadForm = $this->getUploadForm();
        $editorUploadForm = $this->getUploadForm();

        // get a content object
        $contentClass = 'Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content';
        $contentObject = $dm->getRepository($contentClass)->findOneBy(array());

        // Form - content object with file embedded
        $newContentForm = $this->getContentForm();
        $editContentForm = $this->getContentForm($contentObject);

        // action url for editContentForm
        if ($contentObject) {
            $editContentFormAction = $this->generateUrl('phpcr_file_test_content_edit', array(
                'path' => $this->getUrlSafePath($contentObject),
            ));
        } else {
            $editContentFormAction = false;
        }

        return $this->render('::tests/file.html.twig', array(
            'upload_form' => $uploadForm->createView(),
            'editor_form' => $editorUploadForm->createView(),
            'content_form_new' => $newContentForm->createView(),
            'content_form_edit' => $editContentForm->createView(),
            'content_form_edit_action' => $editContentFormAction,
            'files' => $files,
        ));
    }

    public function newAction(Request $request)
    {
        $dm = $this->get('doctrine_phpcr')->getManager('default');
        $contentRoot = $dm->find(null, '/test/content');

        if (!$contentRoot) {
            $root = $dm->find(null, '/test');
            $contentRoot = new Generic();
            $contentRoot->setNodename('content');
            $contentRoot->setParent($root);
            $dm->persist($contentRoot);
        }

        $contentObject = new Content();
        $contentObject->setParent($contentRoot);

        $form = $this->getContentForm($contentObject);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                // persist
                $dm = $this->get('doctrine_phpcr')->getManager('default');
                $dm->persist($contentObject);
                $dm->flush();
            }
        }

        return $this->redirect($this->generateUrl('phpcr_file_test'));
    }

    public function uploadAction(Request $request)
    {
        $form = $this->getUploadForm();

        $form->handleRequest($request);

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

        return $this->redirect($this->generateUrl('phpcr_file_test'));
    }

    public function editAction(Request $request, $path)
    {
        $dm = $this->get('doctrine_phpcr')->getManager('default');

        $contentObject = $dm->find(null, $this->mapUrlSafePathToId($path));

        if (!$contentObject || !$contentObject instanceof Content) {
            throw new NotFoundHttpException(sprintf(
                'Object with identifier %s cannot be resolved to a valid instance of Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content',
                $path
            ));
        }

        $form = $this->getContentForm($contentObject);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                // persist
                $dm = $this->get('doctrine_phpcr')->getManager('default');
                $dm->persist($contentObject);
                $dm->flush();
            }
        }

        return $this->redirect($this->generateUrl('phpcr_file_test'));
    }
}
