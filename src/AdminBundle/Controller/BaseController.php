<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\SubmitActionsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;

class BaseController extends Controller
{
    protected function addDefaultSubmitButtons(FormInterface $form)
    {
        $form->add(
            'buttons',
            SubmitActionsType::class,
            [
                'mapped' => false,
                'actions' => [
                    SubmitActionsType::SAVE_AND_KEEP, SubmitActionsType::SAVE_AND_NEW, SubmitActionsType::SAVE_AND_CLOSE
                ]
            ]
        );
    }

    protected function handleSubmitButtons(FormInterface $form, $routeNew, $routeEdit, $routeEditParams, $paginationParams)
    {
        if ($form->get('buttons')->get(SubmitActionsType::SAVE_AND_NEW)->isClicked()) {
            return $this->redirectToRoute($routeNew, $paginationParams);
        }
        if ($form->get('buttons')->get(SubmitActionsType::SAVE_AND_KEEP)->isClicked()) {
            return $this->redirectToRoute(
                $routeEdit,
                array_merge(
                    $routeEditParams,
                    $paginationParams
                )
            );
        }
        return false;
    }
}