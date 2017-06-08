<?php

namespace AdminBundle\Form\Type;

use SiteBundle\Entity\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannerType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    { 
        $builder
            ->add('description', TextType::class, ['label' => 'admin.banner.fields.description'])
            ->add('imageFile', FileType::class, ['label' => 'admin.banner.fields.image_file'])
            ->add('isEnabled', SwitchType::class, ['label' => 'admin.banner.fields.enabled'])
            ->add('publishedAt', DateTimePickerType::class,
                [
                    'label' => 'admin.banner.fields.published_at',
                    'attr' => ['readonly' => true, 'class' => 'form_datetime']
                ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
            'validation_groups' => ['Default', 'create'],
        ]);
    }

}