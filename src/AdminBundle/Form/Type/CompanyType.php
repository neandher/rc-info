<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\Company;
use AdminBundle\Entity\Uf;
use AdminBundle\Repository\UfRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomeFantasia', TextType::class, ['label' => 'admin.company.fields.nomeFantasia'])
            ->add('razaoSocial', TextType::class, ['label' => 'admin.company.fields.razaoSocial'])
            ->add('cnpj', TextType::class, [
                'label' => 'admin.company.fields.cnpj',
                'attr' => ['class' => 'mask_cnpj']
            ])
            ->add('agencia', TextType::class, ['label' => 'admin.company.fields.agencia'])
            ->add('agenciaDigito', TextType::class, ['label' => 'admin.company.fields.agenciaDigito'])
            ->add('conta', TextType::class, ['label' => 'admin.company.fields.conta'])
            ->add('contaDigito', TextType::class, ['label' => 'admin.company.fields.contaDigito'])
            ->add('codigoBanco', TextType::class, ['label' => 'admin.company.fields.codigoBanco'])
            ->add('carteira', TextType::class, ['label' => 'admin.company.fields.carteira'])
            ->add('codigoCliente', TextType::class, ['label' => 'admin.company.fields.codigoCliente'])
            ->add('aceite', TextType::class, ['label' => 'admin.company.fields.aceite'])
            ->add('especieDoc', TextType::class, ['label' => 'admin.company.fields.especieDoc'])
            ->add('juros', PercentType::class, ['label' => 'admin.company.fields.juros'])
            ->add('multa', PercentType::class, ['label' => 'admin.company.fields.multa'])
            ->add('prazoAposVencimento', TextType::class, ['label' => 'admin.company.fields.prazoAposVencimento'])
            ->add('codigoProtesto', TextType::class, ['label' => 'admin.company.fields.codigoProtesto'])
            ->add('prazoProtesto', TextType::class, ['label' => 'admin.company.fields.prazoProtesto'])
            ->add('codigoBaixaDevolucao', TextType::class, ['label' => 'admin.company.fields.codigoBaixaDevolucao'])
            ->add('prazoBaixaDevolucao', TextType::class, ['label' => 'admin.company.fields.prazoBaixaDevolucao'])
            ->add('street', TextType::class, ['label' => 'admin.company.fields.street'])
            ->add('district', TextType::class, ['label' => 'admin.company.fields.district'])
            ->add('city', TextType::class, ['label' => 'admin.company.fields.city'])
            ->add('zipCode', TextType::class, [
                'label' => 'admin.company.fields.zipCode',
                'attr' => ['class' => 'mask_cep']
            ])
            ->add('uf', EntityType::class, [
                'label' => 'admin.company.fields.uf',
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
        $resolver->setDefaults([
            'data_class' => Company::class
        ]);
    }
}
