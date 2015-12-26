<?php

namespace Ecommerce\ItemBundle\Entity;

use Ecommerce\FrontendBundle\Entity\CustomEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class ExtraRepository extends CustomEntityRepository
{
    protected $specialFields = array();

    public function findAllExtraOptions($order = 'ASC', $limit = null)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e');

        $qb->addOrderBy('e.numberOfItems', $order);

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->neq('e.cost', 0.0));
        $and->add($qb->expr()->isNull('e.deleted'));

        $qb->andWhere($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
