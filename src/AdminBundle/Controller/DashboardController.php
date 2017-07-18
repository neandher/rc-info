<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DashboardController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     * @Route("/dashboard", name="admin_dashboard")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $billRepository = $this->getDoctrine()->getRepository(Bill::class);
        $statusEmAberto = $this->getDoctrine()->getRepository(BillStatus::class)->findOneBy(['referency' => BillStatus::BILL_STATUS_EM_ABERTO]);
        $statusPago = $this->getDoctrine()->getRepository(BillStatus::class)->findOneBy(['referency' => BillStatus::BILL_STATUS_PAGO]);

        $request->query->set('num_items', 15);
        $request->query->set('sorting', ['dueDateAt' => 'asc']);

        // TO RECEIVE
        $request->query->set('bill_status', $statusEmAberto->getId());
        $request->query->set('date_start', date('d/m/Y'));
        $request->query->set('date_end', (new \DateTime())->add(new \DateInterval('P30D'))->format('d/m/Y'));

        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $toReceive = $billRepository->findLatest($pagination);
        $toReceiveTotal = $billRepository->getAmountTotal($request->query->all());

        // RECEIVED
        $request->query->set('bill_status', $statusPago->getId());
        $request->query->set('date_start', (new \DateTime())->sub(new \DateInterval('P30D'))->format('d/m/Y'));
        $request->query->set('date_end', date('d/m/Y'));
        $request->query->set('paid', true);

        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $received = $billRepository->findLatest($pagination);
        $receivedTotal = $billRepository->getAmountTotal($request->query->all());

        // OVERDUE
        $request->query->set('bill_status', $statusEmAberto->getId());
        $request->query->set('paid', false);
        $request->query->set('overdue', 'true');
        $request->query->remove('date_start');
        $request->query->remove('date_end');

        $pagination = $this->get('app.util.pagination')->handle($request, Bill::class);

        $overdue = $billRepository->findLatest($pagination);
        $overdueTotal = $billRepository->getAmountTotal($request->query->all());

        return $this->render('admin/dashboard.html.twig', [
            'toReceive' => $toReceive,
            'toReceiveTotal' => $toReceiveTotal,
            'received' => $received,
            'receivedTotal' => $receivedTotal,
            'overdue' => $overdue,
            'overdueTotal' => $overdueTotal
        ]);
    }
}