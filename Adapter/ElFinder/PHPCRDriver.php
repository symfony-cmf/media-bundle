<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Adapter\ElFinder;

use Doctrine\ODM\PHPCR\Document\Generic;
use Doctrine\ODM\PHPCR\Document\Resource;
use FM\ElFinderPHP\Driver\ElFinderVolumeDriver;
use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\File;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

/**
 * @author Sjoerd Peters <sjoerd.peters@gmail.com>
 */
class PHPCRDriver extends ElFinderVolumeDriver
{
    /**
   	 * Driver id
   	 * Must be started from letter and contains [a-z0-9]
   	 * Used as part of volume id
   	 *
   	 * @var string
   	 **/
   	protected $driverId = 'p';

    /**
     * @var \Doctrine\ODM\PHPCR\DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $manager
     */
    function __construct(DocumentManager $manager)
    {
        $this->dm = $manager;

        $opts = array(
            'workspace'     => '',
            'manager'       => '',
        );
        $this->options = array_merge($this->options, $opts);
    }

    /**
   	 * Return parent directory path
   	 *
   	 * @param  string  $path  file path
   	 * @return string
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _dirname($path) {
   		return dirname($path);
   	}

   	/**
   	 * Return file name
   	 *
   	 * @param  string  $path  file path
   	 * @return string
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _basename($path) {
   		return basename($path);
   	}

   	/**
   	 * Join dir name and file name and retur full path
   	 *
   	 * @param  string  $dir
   	 * @param  string  $name
   	 * @return string
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _joinPath($dir, $name) {
   		return $dir.DIRECTORY_SEPARATOR.$name;
   	}

   	/**
   	 * Return normalized path, this works the same as os.path.normpath() in Python
   	 *
   	 * @param  string  $path  path
   	 * @return string
   	 * @author Troex Nevelin
   	 **/
   	protected function _normpath($path) {
   		if (empty($path)) {
   			return '.';
   		}

   		if (strpos($path, '/') === 0) {
   			$initial_slashes = true;
   		} else {
   			$initial_slashes = false;
   		}

   		if (($initial_slashes)
   		&& (strpos($path, '//') === 0)
   		&& (strpos($path, '///') === false)) {
   			$initial_slashes = 2;
   		}

   		$initial_slashes = (int) $initial_slashes;

   		$comps = explode('/', $path);
   		$new_comps = array();
   		foreach ($comps as $comp) {
   			if (in_array($comp, array('', '.'))) {
   				continue;
   			}

   			if (($comp != '..')
   			|| (!$initial_slashes && !$new_comps)
   			|| ($new_comps && (end($new_comps) == '..'))) {
   				array_push($new_comps, $comp);
   			} elseif ($new_comps) {
   				array_pop($new_comps);
   			}
   		}
   		$comps = $new_comps;
   		$path = implode('/', $comps);
   		if ($initial_slashes) {
   			$path = str_repeat('/', $initial_slashes) . $path;
   		}

   		return $path ? $path : '.';
   	}

   	/**
   	 * Return file path related to root dir
   	 *
   	 * @param  string  $path  file path
   	 * @return string
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _relpath($path) {
   		return $path == $this->root ? '' : substr($path, strlen($this->root)+1);
   	}

   	/**
   	 * Convert path related to root dir into real path
   	 *
   	 * @param  string  $path  file path
   	 * @return string
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _abspath($path) {
   		return $path == DIRECTORY_SEPARATOR ? $this->root : $this->root.DIRECTORY_SEPARATOR.$path;
   	}

   	/**
   	 * Return fake path started from root dir
   	 *
   	 * @param  string  $path  file path
   	 * @return string
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _path($path) {
   		return $this->rootName.($path == $this->root ? '' : $this->separator.$this->_relpath($path));
   	}

   	/**
   	 * Return true if $path is children of $parent
   	 *
   	 * @param  string  $path    path to check
   	 * @param  string  $parent  parent path
   	 * @return bool
   	 * @author Dmitry (dio) Levashov
   	 **/
   	protected function _inpath($path, $parent) {
   		return $path == $parent || strpos($path, $parent.DIRECTORY_SEPARATOR) === 0;
   	}

    /**
     * Return stat for given path.
     * Stat contains following fields:
     * - (int)    size    file size in b. required
     * - (int)    ts      file modification time in unix time. required
     * - (string) mime    mimetype. required for folders, others - optionally
     * - (bool)   read    read permissions. required
     * - (bool)   write   write permissions. required
     * - (bool)   locked  is object locked. optionally
     * - (bool)   hidden  is object hidden. optionally
     * - (string) alias   for symlinks - link target path relative to root path. optionally
     * - (string) target  for symlinks - link target path. optionally
     *
     * If file does not exists - returns empty array or false.
     *
     * @param  string $path    file path
     * @return array|false
     * @author Dmitry (dio) Levashov
     **/
    protected function _stat($path)
    {
        /** @var File $doc */
        $doc = $this->dm->find(null, $path);

        if($path == $this->root && !$doc){
            // @TODO not sure if this the best way / place for this. should a user create the media root manually?
            $doc = new Directory();
            $doc->setId($this->root);
            $this->dm->persist($doc);
            $this->dm->flush($doc);
        }

        if(!($doc instanceof HierarchyInterface || $doc instanceof Generic)){
            return false;
        }

        $dir = $doc instanceof DirectoryInterface || $doc instanceof Generic;
//        $ts = $doc->getUpdatedAt() ? $doc->getUpdatedAt()->getTimestamp() : $doc->getCreatedAt()->getTimestamp();

        if($doc instanceof DirectoryInterface && $ua = $doc->getUpdatedAt()){
            $ts = $ua->getTimestamp();
        } elseif ($doc instanceof DirectoryInterface && $ca = $doc->getCreatedAt()) {
            $ts = $ca->getTimestamp();
        } else {
            $dt = new \DateTime();
            $ts = $dt->getTimestamp();
        }

        $stat = array(
            'size' => $dir ? 0 : $doc->getSize(),
            'ts' => $ts,
            'mime' => $dir ? 'directory' : $doc->getContentType(),
            'read' => true,
            'write' => true,
            'locked' => false,
            'hidden' => false,
        );

        return $stat;
    }

    /**
     * Return true if path is dir and has at least one childs directory
     *
     * @param  string $path  dir path
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _subdirs($path)
    {
        $doc = $this->dm->find(null, $path);
        if($doc instanceof DirectoryInterface){
            return count($doc->getChildren()) > 0;
        }
        return false;
    }

    /**
     * Return object width and height
     * Ususaly used for images, but can be realize for video etc...
     *
     * @param  string $path  file path
     * @param  string $mime  file mime type
     * @return string
     * @author Dmitry (dio) Levashov
     **/
    protected function _dimensions($path, $mime)
    {
        //return '';
        // @TODO we can't store the width and height on the current nodeType
        $doc = $this->dm->find(null, $path);
        if($doc instanceof ImageInterface){
            return $doc->getHeight().' x '.$doc->getWidth();
        }

        return '';
    }

    /**
     * Return files list in directory
     *
     * @param  string $path  dir path
     * @return array
     * @author Dmitry (dio) Levashov
     **/
    protected function _scandir($path)
    {
        $doc = $this->dm->find(null, $path);
        $list = array();
        foreach ($doc->getChildren() as $child) {
            $list[] = $child->getId();
        }
        return $list;
    }

    /**
     * Open file and return file pointer
     *
     * @param  string $path  file path
     * @param  bool $write open file for writing
     * @return resource|false
     * @author Dmitry (dio) Levashov
     **/
    protected function _fopen($path, $mode = "rb")
    {
        $doc = $this->dm->find(null, $path);
        if($doc instanceof File){
            return $doc->getContentAsStream();
        }
        return false;
    }

    /**
     * Close opened file
     *
     * @param  resource $fp    file pointer
     * @param  string $path  file path
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _fclose($fp, $path = '')
    {
        return true;
    }

    /**
     * Create dir and return created dir path or false on failed
     *
     * @param  string $path  parent dir path
     * @param string $name  new directory name
     * @return string|bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _mkdir($path, $name)
    {
        if($this->dm->find(null, $dirname = $this->_joinPath($path, $name))){
            return false;
        }

        $dir = new Directory();
        $dir->setName($name);
        $dir->setId($dirname);
        $this->dm->persist($dir);
        $this->dm->flush($dir);
        return $dirname;
    }

    /**
     * Create file and return it's path or false on failed
     *
     * @param  string $path  parent dir path
     * @param string $name  new file name
     * @return string|bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _mkfile($path, $name)
    {
        if($this->dm->find(null, $filename = $this->_joinPath($path, $name))){
            return false;
        }

        $file = new File();
        $file->setContentFromString('');
        $file->setId($filename);

        // @TODO failing cascade persist
        $content = $file->getContent();
        $content->setParent($file);

        $pi = pathinfo($filename);
        if(isset($pi['extension']) && !empty($pi['extension'])){
            if(isset(self::$mimetypes[$pi['extension']])){
                $file->setContentType(self::$mimetypes[$pi['extension']]);
            }
        }

        $this->dm->persist($content);
        $this->dm->persist($file);
        $this->dm->flush();

        return $filename;
    }

    /**
     * Create symlink
     *
     * @param  string $source     file to link to
     * @param  string $targetDir  folder to create link in
     * @param  string $name       symlink name
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _symlink($source, $targetDir, $name)
    {
        // TODO: Implement _symlink() method.
    }

    /**
     * Copy file into another file (only inside one volume)
     *
     * @param  string $source  source file path
     * @param  string $targetDir  target dir path
     * @param  string $name    file name
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _copy($source, $targetDir, $name)
    {
        if($this->dm->find(null, $targetPath = $this->_joinPath($targetDir, $name))){
            return false;
        }

        $this->dm->getPhpcrSession()->getWorkspace()->copy($source, $targetPath);
        return true;
    }

    /**
     * Move file into another parent dir.
     * Return new file path or false.
     *
     * @param  string $source  source file path
     * @param  string $targetDir  target dir path
     * @param  string $name    file name
     * @return string|bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _move($source, $targetDir, $name)
    {
        try {
            $doc = $this->dm->find(null, $source);
            $this->dm->move($doc, $this->_joinPath($targetDir, $name));
            $this->dm->flush();
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Remove file
     *
     * @param  string $path  file path
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _unlink($path)
    {
        try {
            $doc = $this->dm->find(null, $path);
            $this->dm->remove($doc);
            $this->dm->flush();
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Remove dir
     *
     * @param  string $path  dir path
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _rmdir($path)
    {
        if($doc = $this->dm->find(null, $path)){
            try {
                $this->dm->remove($doc);
                $this->dm->flush($doc);
                return true;
            } catch(\Exception $e){
            }
        }
        return false;
    }

    /**
     * Create new file and write into it from file pointer.
     * Return new file path or false on error.
     *
     * @param  resource $fp   file pointer
     * @param  string $dir  target dir path
     * @param  string $name file name
     * @param  array $stat file stat (required by some virtual fs)
     * @return bool|string
     * @author Dmitry (dio) Levashov
     **/
    protected function _save($fp, $dir, $name, $stat)
    {
        $filename = $this->_joinPath($dir, $name);

        if($this->dm->find(null, $filename)){
            return false;
        }

        $mime = $stat['mime']; // @TODO implement a proper system to map a mime-type to a phpcr class
        if(isset($stat['height']) && $stat['height'] && isset($stat['width']) && $stat['width']){
            $file = new Image();
        } else {
            $file = new File();
        }

        $file->setContentFromStream($fp);
        $file->setContentType($mime);
        $file->setId($filename);

//        try {
        $this->dm->persist($file);
        $this->dm->flush();
//        } catch (\Exception $e) {
//            return false;
//        }

        return $filename;
    }

    /**
     * Get file contents
     *
     * @param  string $path  file path
     * @return string|false
     * @author Dmitry (dio) Levashov
     **/
    protected function _getContents($path)
    {
        /** @var File $doc */
        $doc = $this->dm->find(null, $path);

        if(!$doc instanceof File){
            return false;
        }
        return $doc->getContentAsStream();
    }

    /**
     * Write a string to a file
     *
     * @param  string $path     file path
     * @param  string $content  new file content
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    protected function _filePutContents($path, $content)
    {
        /** @var File $doc */
        $doc = $this->dm->find(null, $path);

        if(!$doc instanceof File){
            return false;
        }
        return $doc->setContentFromString($content);
    }

    /**
     * Extract files from archive
     *
     * @param  string $path file path
     * @param  array $arc  archiver options
     * @return bool
     * @author Dmitry (dio) Levashov,
     * @author Alexey Sukhotin
     **/
    protected function _extract($path, $arc)
    {
        // TODO: Implement _extract() method.
    }

    /**
     * Create archive and return its path
     *
     * @param  string $dir    target dir
     * @param  array $files  files names list
     * @param  string $name   archive name
     * @param  array $arc    archiver options
     * @return string|bool
     * @author Dmitry (dio) Levashov,
     * @author Alexey Sukhotin
     **/
    protected function _archive($dir, $files, $name, $arc)
    {
        // TODO: Implement _archive() method.
    }

    /**
     * Detect available archivers
     *
     * @return void
     * @author Dmitry (dio) Levashov,
     * @author Alexey Sukhotin
     **/
    protected function _checkArchivers()
    {
        // TODO: Implement _checkArchivers() method.
    }


}
