<?php

namespace AdminBundle\Repository;

use AppBundle\Repository\BaseRepository;
use AppBundle\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * VideoCategoryRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class VideoCategoryRepository extends BaseRepository
{
    protected function queryLatest($routeParams)
    {
        $qb = $this->createQueryBuilder('c')
            ->distinct(true)
            ->join('c.videos', 'v');

        $qb->addSelect("COUNT(v.id) as videosCount");;

        if (isset($routeParams['search'])) {
            $qb->andWhere(
                $qb->expr()->like('c.description', ':search')
            )->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!empty($routeParams['enabled'])) {
            $qb->andWhere('c.isEnabled = 1');
        }

        if (!empty($routeParams['publishedOnly'])) {
            $qb->andWhere(':now >= c.publishedAt')->setParameter('now', new \DateTime());
        }

        $qb = $this->addOrderingQueryBuilder($qb, $routeParams);

        $qb->addGroupBy('c.id');

        return $qb->getQuery();
    }

    public function findLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest($routeParams), false));

        $paginator->setMaxPerPage($routeParams['num_items']);
        $paginator->setCurrentPage($routeParams['page']);

        return $paginator;
    }

    public function findLatestPortal(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $routeParams['sorting'] = ['publishedAt' => 'desc'];
        $routeParams['enabled'] = true;
        $routeParams['publishedOnly'] = true;

        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest($routeParams), false));

        $paginator->setMaxPerPage($routeParams['num_items']);
        $paginator->setCurrentPage($routeParams['page']);

        return $paginator;
    }

    public function queryLatestForm()
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.description', 'ASC');
    }
}
