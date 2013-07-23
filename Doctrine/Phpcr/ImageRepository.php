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
    public function getImagesByName($name, $offset, $limit)
    {
        $qb = $this->createQueryBuilder();

        if ($this->rootPath) {
            $qb->andWhere($qb->expr()->descendant($this->rootPath));
        }

        if (strlen($name)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->likeNodeName('%'.$name.'%'),
                    $qb->expr()->like('description', '%'.$name.'%')
                )
            );
        }

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->execute();
    }
}