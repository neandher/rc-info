<?php

namespace AdminBundle\Repository;
use AppBundle\Repository\BaseRepository;
use AppBundle\Util\Pagination;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * BillRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class BillRepository extends BaseRepository
{
    protected function queryLatest(Pagination $pagination)
    {
        $routeParams = $pagination->getRouteParams();

        $qb = $this->createQueryBuilder('bill')
            ->innerJoin('bill.billStatus', 'billStatus')
            ->addSelect('billStatus')
            ->innerJoin('bill.customer', 'customer')
            ->addSelect('customer');

        if (!empty($routeParams['search'])) {
            $qb
                ->andWhere('bill.description LIKE :search')
                ->orWhere('customer.name LIKE :search')
                ->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!empty($routeParams['bill_status'])) {
            $qb->andWhere('billStatus.id = :bill_status')->setParameter('bill_status', $routeParams['bill_status']);
        }

        if ((isset($routeParams['date_start']) && !empty($routeParams['date_start'])) && (isset($routeParams['date_end']) && !empty($routeParams['date_end']))) {

            $date_start = \DateTime::createFromFormat('d/m/Y', $routeParams['date_start'])->format('Y-m-d');
            $date_end = \DateTime::createFromFormat('d/m/Y', $routeParams['date_end'])->format('Y-m-d');

            $qb->andWhere('bill.dueDateAt >= :date_start')->setParameter('date_start', $date_start);
            $qb->andWhere('bill.dueDateAt <= :date_end')->setParameter('date_end', $date_end);
        }

        if (!isset($routeParams['sorting'])) {
            $qb->orderBy('bill.id', 'desc');
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
