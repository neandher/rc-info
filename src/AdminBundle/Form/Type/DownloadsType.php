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
            ->add('imageFile', FileType::class, [
                'label' => 'admin.downloads.fields.image_file',
                'attr' => ['class' => 'form-control']
            ])
            ->add('downloadFile', FileType::class, [
                'label' => 'admin.downloads.fields.download_file',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isEnabled', SwitchType::class, ['label' => 'admin.downloads.fields.enabled']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Downloads::class,
            'validation_groups' => ['Default', 'create'],
        ]);
    }
}
