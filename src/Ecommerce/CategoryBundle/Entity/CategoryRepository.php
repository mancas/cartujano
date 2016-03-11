<?php

namespace Ecommerce\CategoryBundle\Entity;

use Ecommerce\FrontendBundle\Entity\CustomEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends CustomEntityRepository
{
    protected $specialFields = array();

    public function findCategoriesDQL($limit = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c','s');

        $qb->leftJoin('c.subcategories', 's');
        $qb->addOrderBy('c.id','ASC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->isNull('c.deleted'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findSEOCategories($limit = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c');

        $qb->addOrderBy('c.updated','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('c.useInIndex', true));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
