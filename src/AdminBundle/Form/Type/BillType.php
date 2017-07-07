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

        $builder
            ->add('description', TextType::class, [
                'label' => 'admin.bill.fields.description',
                'required' => false
            ])
            ->add('dueDateAt', DatePickerType::class, ['label' => 'admin.bill.fields.dueDateAt'])
            ->add('paymentDateAt', DatePickerType::class, [
                'label' => 'admin.bill.fields.paymentDateAt',
                'required' => false
            ])
            ->add('amount', MoneyCustomType::class, ['label' => 'admin.bill.fields.amount'])
            ->add('amountPaid', MoneyCustomType::class, [
                'label' => 'admin.bill.fields.amountPaid',
                'required' => false
            ])
            ->add('note', TextareaType::class, [
                'label' => 'admin.bill.fields.note',
                'required' => false
            ])
            ->add('customerChoices', CustomerChoicesType::class);

        if ($remessaSent) {
            $builder->remove('dueDateAt')
                ->remove('amount')
                ->remove('customerChoices');
        }
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
