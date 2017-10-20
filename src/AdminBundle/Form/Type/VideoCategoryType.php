<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\VideoCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoCategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'admin.video_category.fields.description'])
            ->add('isEnabled', SwitchType::class, ['label' => 'admin.video_category.fields.enabled'])
            ->add(
                'publishedAt',
                DateTimePickerType::class,
                [
                    'label' => 'admin.video_category.fields.published_at',
                    'attr'  => ['readonly' => true, 'class' => 'form_datetime'],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => VideoCategory::class,
            ]
        );
    }
}