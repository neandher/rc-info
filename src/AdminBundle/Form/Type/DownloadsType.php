<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\Downloads;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'admin.downloads.fields.description'])
            ->add('downloadFile', FileType::class, [
                'label' => 'admin.downloads.fields.download_file',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isEnabled', SwitchType::class, ['label' => 'admin.downloads.fields.enabled'])
            ->add('publishedAt', DateTimePickerType::class,
                [
                    'label' => 'admin.downloads.fields.published_at',
                    'attr' => ['readonly' => true, 'class' => 'form_datetime']
                ]);

        if ($options['is_edit']) {
            $builder->remove('downloadFile');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Downloads::class,
            'validation_groups' => ['Default', 'create'],
            'is_edit' => false
        ]);
    }
}