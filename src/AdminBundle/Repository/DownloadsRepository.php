<?php

namespace AdminBundle\Repository;

use AppBundle\Repository\BaseRepository;
use AppBundle\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * DownloadsRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class DownloadsRepository extends BaseRepository
{
    protected function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('d');

        if (isset($routeParams['search'])) {
            $qb->andWhere(
                $qb->expr()->like('d.description', ':search')
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
