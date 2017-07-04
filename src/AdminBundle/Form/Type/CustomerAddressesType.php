<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\Uf;
use AdminBundle\Repository\UfRepository;
use SiteBundle\Entity\CustomerAddresses;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerAddressesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', TextType::class, ['label' => 'admin.customerAddresses.fields.street'])
            ->add('district', TextType::class, ['label' => 'admin.customerAddresses.fields.district'])
            ->add('city', TextType::class, ['label' => 'admin.customerAddresses.fields.city'])
            ->add('postcode', TextType::class, [
                'label' => 'admin.customerAddresses.fields.postcode',
                'attr' => ['class' => 'mask_cep']
            ])
            ->add('mainAddress', SwitchType::class, ['label' => 'admin.customerAddresses.fields.mainAddress'])
            ->add('complement', TextType::class, ['label' => 'admin.customerAddresses.fields.complement'])
            ->add('uf', EntityType::class, [
                'class' => Uf::class,
                'query_builder' => function (UfRepository $er) {
                    return $er->queryLatest();
                }
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CustomerAddresses::class
        ));
    }
}
