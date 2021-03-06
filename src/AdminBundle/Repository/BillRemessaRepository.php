<?php

namespace AdminBundle\Repository;

use AppBundle\Repository\BaseRepository;
use AppBundle\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * BillRemessaRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class BillRemessaRepository extends BaseRepository
{
    protected function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('rem');

        if (!empty($routeParams['search'])) {
            $qb
                ->andWhere('rem.description LIKE :search')
                ->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!isset($routeParams['sorting'])) {
            $qb->orderBy('rem.id', 'desc');
        } else {
            $qb = $this->addOrderingQueryBuilder($qb, $routeParams);
        }

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
