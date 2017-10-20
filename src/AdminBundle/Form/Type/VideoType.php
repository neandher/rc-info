<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\video;
use AdminBundle\Entity\VideoCategory;
use AdminBundle\Repository\VideoCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['label' => 'admin.video.fields.description'])
            ->add('category', EntityType::class, [
                'class' => VideoCategory::class,
                'query_builder' => function (VideoCategoryRepository $er) {
                    return $er->queryLatestForm();
                },
                'choice_label' => 'description',
                'label' => 'admin.video.fields.category',
                'required' => false
            ])
            ->add(
                'url',
                UrlType::class,
                [
                    'label' => 'admin.video.fields.url',
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add('isEnabled', SwitchType::class, ['label' => 'admin.video.fields.enabled'])
            ->add(
                'publishedAt',
                DateTimePickerType::class,
                [
                    'label' => 'admin.video.fields.published_at',
                    'attr' => ['readonly' => true, 'class' => 'form_datetime'],
                ]
            )
            ->add('text', TextareaType::class, ['label' => 'admin.video.fields.text']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => video::class,
            ]
        );
    }
}