<?php

namespace Ecommerce\ItemBundle\Entity;

use Ecommerce\FrontendBundle\Entity\CustomEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class ItemRepository extends CustomEntityRepository
{
    protected $specialFields = array('name');

    protected function addToQueryBuilderSpecialFieldName(\Doctrine\ORM\QueryBuilder &$qb, $value)
    {
        $qb->andWhere($qb->expr()->like('i.name', $qb->expr()->literal('%'. $value . '%')));
    }

    protected function addSpecialSearchCriteria(&$qb)
    {
        $qb->andWhere($qb->expr()->isNull('i.deleted'));
    }

    public function findRecentItemsDQL($limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');

        $qb->addOrderBy('p.updated','DESC');

        $and = $qb->expr()->andx();

        //$and->add($qb->expr()->eq('p.isCommercialItem', 0));
        $and->add($qb->expr()->isNull('p.deleted'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findCommercialItemsDQL($limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->leftJoin('p.images', 'i');

        $qb->addOrderBy('p.updated','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('p.isCommercialItem', 1));
        $and->add($qb->expr()->isNull('p.deleted'));
        $and->add($qb->expr()->isNull('i.deletedDate'));
        $and->add($qb->expr()->isNotNull('i.createDate'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findCategorySEOItemsDQL($category, $limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');

        $qb->leftJoin('p.images', 'i');
        $qb->leftJoin('p.subcategory', 's');
        $qb->leftJoin('s.category', 'c');
        $qb->addOrderBy('p.updated','ASC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('c.slug', '\''. $category->getSlug() .'\''));
        //$and->add($qb->expr()->eq('p.isCommercialItem', 0));
        $and->add($qb->expr()->isNull('p.deleted'));
        $and->add($qb->expr()->isNull('i.deletedDate'));
        $and->add($qb->expr()->isNotNull('i.createDate'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findRelatedItems($item, $limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p','i', 's');

        $qb->leftJoin('p.images', 'i');
        $qb->leftJoin('p.subcategory', 's');
        $qb->addOrderBy('p.updated','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->neq('p.slug', "'" . $item->getSlug() . "'"));
        $and->add($qb->expr()->eq('s.slug', "'" . $item->getSubcategory()->getSlug() . "'"));
        //$and->add($qb->expr()->eq('p.isCommercialItem', 0));
        $and->add($qb->expr()->isNull('p.deleted'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findItemsBySubcategoryDQL($subcategory, $limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p','i', 's');

        $qb->leftJoin('p.images', 'i');
        $qb->leftJoin('p.subcategory', 's');
        $qb->addOrderBy('p.updated','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('s.slug', "'" . $subcategory->getSlug() . "'"));
        //$and->add($qb->expr()->eq('p.isCommercialItem', 0));
        $and->add($qb->expr()->isNull('p.deleted'));
        $and->add($qb->expr()->isNull('i.deletedDate'));
        $and->add($qb->expr()->isNotNull('i.createDate'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery();
    }

    public function findItemsByPackageDQL($package, $limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');

        $qb->leftJoin('p.package', 'pa');
        $qb->addOrderBy('p.updated','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->eq('pa.id', $package));
        //$and->add($qb->expr()->eq('p.isCommercialItem', 0));
        $and->add($qb->expr()->isNull('p.deleted'));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery();
    }

    public function findItemsBySearchText($search, $limit = null)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p','i', 's');

        $qb->leftJoin('p.images', 'i');
        $qb->leftJoin('p.subcategory', 's');
        $qb->addOrderBy('p.updated','DESC');

        $and = $qb->expr()->andx();

        $and->add($qb->expr()->like('p.name', $qb->expr()->literal('%' . $search . '%')));
        //$and->add($qb->expr()->eq('p.isCommercialItem', 0));
        $and->add($qb->expr()->isNull('p.deleted'));
        $and->add($qb->expr()->isNull('i.deletedDate'));
        $and->add($qb->expr()->isNotNull('i.createDate'));
        //$qb->andHaving($qb->expr()->gt($qb->expr()->count('i.id'), 0));

        $qb->where($and);

        if (isset($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
