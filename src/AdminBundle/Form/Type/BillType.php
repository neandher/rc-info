<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\Bill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Bill $bill */
        $bill = $builder->getData();

        $remessaSent = false;

        if ($options['is_edit']) {
            $remessaSent = $bill->getBillRemessa()->getSent();
        }

        $arrayDueDateAt = [
            'label' => 'admin.bill.fields.dueDateAt',
        ];

        $arrayAmount = [
            'label' => 'admin.bill.fields.amount'
        ];

        $arrayCustomer = [];

        if ($options['is_edit'] && $remessaSent) {

            $arrayDueDateAt = array_merge($arrayDueDateAt, [
                'attr' => [
                    'class' => '',
                    'readonly' => true
                ]
            ]);

            $arrayAmount = array_merge($arrayAmount, [
                'attr' => [
                    'readonly' => true
                ]
            ]);

            $arrayCustomer = array_merge($arrayAmount, [
                'attr' => [
                    'readonly' => true
                ]
            ]);
        }

        $builder
            ->add('description', TextType::class, [
                'label' => 'admin.bill.fields.description'
            ])
            ->add('dueDateAt', DatePickerType::class, $arrayDueDateAt)
            ->add('paymentDateAt', DatePickerType::class, [
                'label' => 'admin.bill.fields.paymentDateAt',
                'required' => false
            ])
            ->add('amount', MoneyCustomType::class, $arrayAmount)
            ->add('amountPaid', MoneyCustomType::class, [
                'label' => 'admin.bill.fields.amountPaid',
                'required' => false
            ])
            ->add('note', TextareaType::class, [
                'label' => 'admin.bill.fields.note',
                'required' => false
            ])
            ->add('customerChoices', CustomerChoicesType::class, $arrayCustomer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class,
            'is_edit' => false
        ]);
    }

}
