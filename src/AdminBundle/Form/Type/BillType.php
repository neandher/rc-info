<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\Bill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('amount', TextType::class, ['label' => 'admin.bill.fields.amount'])
            ->add('amountPaid', TextType::class, [
                'label' => 'admin.bill.fields.amountPaid',
                'required' => false
            ])
            ->add('note', TextareaType::class, [
                'label' => 'admin.bill.fields.note',
                'required' => false
            ])
            ->add('customerChoices', CustomerChoicesType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Bill::class
        ));
    }

}
