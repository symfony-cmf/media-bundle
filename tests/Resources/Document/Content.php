<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Tests\Resources\Document;

use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * @PHPCRODM\Document(referenceable=true)
 */
class Content
{
    /**
     * @PHPCRODM\Child(cascade="persist")
     */
    protected $file;

    /**
     * @PHPCRODM\Id(strategy="parent")
     */
    protected $id;

    /**
     * @PHPCRODM\ParentDocument
     */
    protected $parent;

    /**
     * @PHPCRODM\NodeName
     */
    protected $name;

    /**
     * @PHPCRODM\Field(type="string")
     */
    protected $title;

    /**
     * Set the file for this block.
     *
     * Setting null will do nothing, as this is what happens when you edit this
     * block in a form without uploading a replacement file.
     *
     * If you need to delete the file, you can use getFile and delete it with
     * the document manager. Note that this block does not make much sense
     * without a file, though.
     *
     * @param FileInterface|UploadedFile|null $file optional the file to update
     */
    public function setFile($file = null)
    {
        if (!$file) {
            return;
        }

        if (!$file instanceof FileInterface && !$file instanceof UploadedFile) {
            $type = is_object($file) ? get_class($file) : gettype($file);

            throw new \InvalidArgumentException(sprintf(
                'File is not a valid type, "%s" given.',
                $type
            ));
        }

        if ($this->file) {
            // existing file, only update content
            // TODO: https://github.com/doctrine/phpcr-odm/pull/262
            $this->file->copyContentFromFile($file);
        } elseif ($file instanceof FileInterface) {
            $file->setName('file'); // Ensure node name matches document mapping
            $this->file = $file;
        } else {
            $this->file = new File();
            $this->file->copyContentFromFile($file);
        }
    }

    /**
     * Get file.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
}
