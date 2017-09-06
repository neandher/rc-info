<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\CMSDownloads;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CMSDownloadsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'admin.cms_downloads.fields.description'])
            ->add('imageFile', FileType::class, ['label' => 'admin.cms_downloads.fields.image_file'])
            ->add('url', UrlType::class, [
                'label' => 'admin.cms_downloads.fields.url',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isEnabled', SwitchType::class, ['label' => 'admin.cms_downloads.fields.enabled'])
            ->add('publishedAt', DateTimePickerType::class,
                [
                    'label' => 'admin.cms_downloads.fields.published_at',
                    'attr' => ['readonly' => true, 'class' => 'form_datetime']
                ])
            ->add('text', TextareaType::class, ['label' => 'admin.cms_downloads.fields.text']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CMSDownloads::class,
            'validation_groups' => ['Default', 'create'],
            'is_edit' => false
        ]);
    }
}