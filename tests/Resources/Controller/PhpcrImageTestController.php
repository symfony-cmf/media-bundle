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
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content;
use Symfony\Cmf\Bundle\MediaBundle\Util\LegacyFormHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhpcrImageTestController extends Controller
{
    protected function getUploadForm()
    {
        return $this->container->get('form.factory')->createNamedBuilder(null, LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\FormType'))
            ->add('image', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\FileType'))
            ->getForm()
        ;
    }

    protected function getContentForm(Content $contentObject = null, array $imageOptions = array())
    {
        if (is_null($contentObject)) {
            $contentObject = new Content();
        }

        return $this->createFormBuilder($contentObject)
            ->add('name')
            ->add('title')
            ->add('file', LegacyFormHelper::getType('Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType'), array_merge(array('required' => false, 'label' => 'Image'), $imageOptions))
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

    protected function getImageContentObject($contentObjects)
    {
        if (is_null($contentObjects)) {
            return;
        }
        /** @var Content $contentObject */
        foreach ($contentObjects as $contentObject) {
            if ($contentObject->getFile() instanceof Image) {
                return $contentObject;
            }
        }
    }

    public function indexAction(Request $request)
    {
        $dm = $this->get('doctrine_phpcr')->getManager('default');

        // get image(s)
        $imageClass = 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image';
        $images = $dm->getRepository($imageClass)->findAll();

        // get content with image object
        $contentClass = 'Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document\Content';
        $contentObject = $this->getImageContentObject($dm->getRepository($contentClass)->findAll());

        $uploadForm = $this->getUploadForm();
        $editorUploadForm = $this->getUploadForm();

        // Form - content object with image embedded
        $newContentForm = $this->getContentForm(null, array('required' => true));
        $contentForm = $this->getContentForm($contentObject, array('imagine_filter' => false));
        $contentFormImagine = $this->getContentForm($contentObject);

        // action url
        if ($contentObject) {
            $contentFormEditAction = $this->generateUrl('phpcr_image_test_content_edit', array(
                'path' => $this->getUrlSafePath($contentObject),
            ));
        } else {
            $contentFormEditAction = false;
        }

        return $this->render('::tests/image.html.twig', array(
            'upload_form' => $uploadForm->createView(),
            'editor_form' => $editorUploadForm->createView(),
            'content_form_new' => $newContentForm->createView(),
            'content_form' => $contentForm->createView(),
            'content_form_imagine' => $contentFormImagine->createView(),
            'content_form_edit_action' => $contentFormEditAction,
            'images' => $images,
        ));
    }

    public function uploadAction(Request $request)
    {
        $form = $this->getUploadForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            /* @var UploadFileHelperInterface $uploadFileHelper */
            $uploadImageHelper = $this->get('cmf_media.upload_image_helper');

            $uploadedFile = $request->files->get('image');

            /** @var Image $image */
            $image = $uploadImageHelper->handleUploadedFile($uploadedFile);
            $image->setMetadataValue('a', 'b');

//            $image->setMetadataValue('a', 'b');

            // persist
            $dm = $this->get('doctrine_phpcr')->getManager('default');
            $dm->persist($image);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('phpcr_image_test'));
    }

    public function newAction(Request $request)
    {
        $dm = $this->get('doctrine_phpcr')->getManager('default');
        $contentRoot = $dm->find(null, '/test/content');

        if (!$contentRoot) {
            $root = $dm->find(null, '/test');
            $contentRoot = new Generic();
            $contentRoot->setNodename('content');
            $contentRoot->setParentDocument($root);
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

        return $this->redirect($this->generateUrl('phpcr_image_test'));
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

        return $this->redirect($this->generateUrl('phpcr_image_test'));
    }
}
