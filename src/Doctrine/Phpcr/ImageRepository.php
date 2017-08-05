<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Symfony\Cmf\Bundle\MediaBundle\Model\ImageRepositoryInterface;

class ImageRepository extends DocumentRepository implements ImageRepositoryInterface
{
    protected $rootPath = '/';

    /**
     * {@inheritdoc}
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function searchImages($term, $limit = 0, $offset = 0)
    {
        $qb = $this->createQueryBuilder('i');

        if ($this->rootPath) {
            $qb->andWhere()->descendant($this->rootPath, 'i');
        }

        if (strlen($term)) {
            $qb->andWhere()->orX()
                ->like()->localName('i')->literal('%'.$term.'%')->end()
                ->like()->field('i.description')->literal('%'.$term.'%')
            ;
        }

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->execute();
    }
}
