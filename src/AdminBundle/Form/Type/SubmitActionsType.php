<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitActionsType extends AbstractType
{
    const SAVE_AND_KEEP = 'save_and_keep';
    const SAVE_AND_NEW = 'save_and_new';
    const SAVE_AND_CLOSE = 'save_and_close';
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (in_array(self::SAVE_AND_CLOSE, $options['actions'])) {
            $builder->add(self::SAVE_AND_CLOSE, SubmitType::class, ['label' => 'form.submit_actions.save_and_close']);
        }
        if (in_array(self::SAVE_AND_NEW, $options['actions'])) {
            $builder->add(self::SAVE_AND_NEW, SubmitType::class, ['label' => 'form.submit_actions.save_and_new']);
        }
        if (in_array(self::SAVE_AND_KEEP, $options['actions'])) {
            $builder->add(self::SAVE_AND_KEEP, SubmitType::class, ['label' => 'form.submit_actions.save_and_keep']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'actions' => []
            ]
        );
    }
}