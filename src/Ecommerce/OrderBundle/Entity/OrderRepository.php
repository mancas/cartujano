<?php

namespace Ecommerce\OrderBundle\Entity;

use Ecommerce\FrontendBundle\Entity\CustomEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class OrderRepository extends CustomEntityRepository
{
    protected $specialFields = array();

    public function findBySearchCriteria($criteria)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o');

        if (isset($criteria['from'])) {
            $qb->andWhere($qb->expr()->gte('o.date', '\''.$criteria['from']->format('Y-m-d H:i:s').'\''));
        }

        if (isset($criteria['to'])) {
            $dateTo = $criteria['to'];
            $dateTo->modify('+1 day');
            $qb->andWhere($qb->expr()->lte('o.date', '\''.$criteria['to']->format('Y-m-d H:i:s').'\''));
        }

        if (isset($criteria['payment']) && $criteria['payment']) {
            $qb->andWhere($qb->expr()->isNotNull('o.payment'));
        }

        if (isset($criteria['status'])) {
            $qb->andWhere($qb->expr()->eq('o.status', '\''.$criteria['status'].'\''));
        }

        if (isset($criteria['shipment'])) {
            $qb->leftJoin('o.shipment', 's');
            $qb->andWhere($qb->expr()->eq('s.id', '\''.$criteria['shipment']->getId().'\''));
        }

        return $qb->getQuery()->getResult();
    }

    public function findOrdersByUserEmailDQL($userEmail, $limit = null)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o');

        $qb->leftJoin('o.customer', 'u');
        $qb->addOrderBy('o.date','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('u.email', '\''. $userEmail .'\''));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery();
    }

    public function findOrdersByUserEmail($userEmail, $limit = null)
    {
        return $this->findOrdersByUserEmailDQL($userEmail, $limit)->getResult();
    }

    public function findOrdersResume($limit = null)
    {
        $dateFrom = new \DateTime('now');
        $dateTo = new \DateTime('now');
        $dateFrom->modify('-7 days');

        $qb = $this->createQueryBuilder('o');
        $qb->select('COUNT(o) as TotalOrders, o.date');
        $qb->addOrderBy('o.date', 'ASC');
        $qb->addGroupBy('o.date');

        $and = $qb->expr()->andx();
        $and->add($qb->expr()->gte('o.date', '\''.$dateFrom->format('Y-m-d H:i:s').'\''));
        $and->add($qb->expr()->lte('o.date', '\''.$dateTo->format('Y-m-d H:i:s').'\''));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOrdersReadyToSend()
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o');
        $qb->addOrderBy('o.date', 'ASC');

        $and = $qb->expr()->andx();
        $and->add($qb->expr()->eq('o.status', '\''.Order::STATUS_READY.'\''));
        $and->add($qb->expr()->isNotNull('o.address'));

        $qb->where($and);

        return $qb->getQuery()->getResult();
    }

    public function findOrdersReadyToTake()
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('o');
        $qb->addOrderBy('o.date', 'ASC');

        $and = $qb->expr()->andx();
        $and->add($qb->expr()->eq('o.status', '\''.Order::STATUS_IN_PROCESS.'\''));
        $and->add($qb->expr()->isNull('o.address'));

        $qb->where($and);

        return $qb->getQuery()->getResult();
    }
}
