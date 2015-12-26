<?php

namespace Ecommerce\PaymentBundle\Entity;

use Ecommerce\FrontendBundle\Entity\CustomEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class BankAccountRepository extends CustomEntityRepository
{
    public function findBankAccount()
    {
        $qb = $this->createQueryBuilder('ba');
        $qb->select('ba');

        return $qb->getQuery()->getResult();
    }
}