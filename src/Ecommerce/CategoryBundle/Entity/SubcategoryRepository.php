<?php

namespace Ecommerce\CategoryBundle\Entity;

use Ecommerce\FrontendBundle\Entity\CustomEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class SubcategoryRepository extends CustomEntityRepository
{
    protected $specialFields = array();

    public function findSubcategoriesFromParentCategoryDQL($category, $limit = null)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s', 'p');

        $qb->leftJoin('s.category', 'c');
        $qb->leftJoin('s.items', 'p');
        $qb->addOrderBy('s.id','ASC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('c.id', $category));
        $and->add($qb->expr()->isNull('s.deleted'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
