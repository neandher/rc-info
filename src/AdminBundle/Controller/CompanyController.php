<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Company;
use AdminBundle\Entity\CompanyStatus;
use AdminBundle\Event\CompanyEvents;
use AdminBundle\Form\Type\CompanyType;
use AppBundle\Event\FlashBagEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CompanyController
 * @package AppBundle\Controller\Admin
 *
 * @Route("/company")
 */
class CompanyController extends BaseController
{
    /**
     * @Route("/", name="admin_company_index")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $companys = $this->getDoctrine()->getRepository(Company::class)->findAll();

        if (count($companys) > 0) {
            return $this->redirectToRoute('admin_company_edit', [
                'id' => $companys[0]->getId()
            ]);
        }

        return $this->redirectToRoute('admin_company_new');
    }

    /**
     * @Route("/new", name="admin_company_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Company::class);

        $company = new Company();
        $company->setMainAddress(true);

        $form = $this->createForm(CompanyType::class, $company);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_company_new',
                'admin_company_edit',
                ['id' => $company->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_company_index');
        }

        return $this->render('admin/company/new.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="admin_company_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Company $company, Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, Company::class);

        $form = $this->createForm(CompanyType::class, $company);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            $this->get('app.util.flash_bag')->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_company_new',
                'admin_company_edit',
                ['id' => $company->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_company_index');
        }

        return $this->render('admin/company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}