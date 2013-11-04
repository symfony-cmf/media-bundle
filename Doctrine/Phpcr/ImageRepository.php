<?php

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\DocumentRepository;
use PHPCR\Query\QueryInterface;
use Symfony\Cmf\Bundle\MediaBundle\Model\ImageRepositoryInterface;

class ImageRepository extends DocumentRepository implements ImageRepositoryInterface
{
    protected $rootPath = '/';

    /**
     * {@inheritDoc}
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;

        return $this;
    }

    /**
     * {@inheritDoc}
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