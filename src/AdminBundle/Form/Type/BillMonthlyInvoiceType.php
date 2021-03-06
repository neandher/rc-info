<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use UserBundle\Form\Type\PlainPasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillMonthlyInvoiceType extends PlainPasswordType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Mês/Ano',
                'format' => 'yyyy-MMMM-dd',
                'years' => range(date('Y'), date('Y') + 5),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}