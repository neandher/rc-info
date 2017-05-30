<?php

namespace SiteBundle\Repository;

use AppBundle\Repository\BaseRepository;
use AppBundle\Util\Pagination;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * CustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerRepository extends BaseRepository
{
    protected function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('customer')
            ->leftJoin('customer.siteUser', 'site_user')
            ->addSelect('site_user');

        if (isset($routeParams['search'])) {
            $qb->andWhere(
                $qb->expr()->like('customer.name', ':search')
            )->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        $qb = $this->addOrderingQueryBuilder($qb, $routeParams);

        return $qb->getQuery();
    }


    public function findLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest($pagination), false));

        $paginator->setMaxPerPage($routeParams['num_items']);
        $paginator->setCurrentPage($routeParams['page']);

        return $paginator;
    }
}
