<?php

namespace AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTimeTransformer implements DataTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function transform($datetime)
    {
        if (null === $datetime) {
            return;
        }

        return $datetime->format('d/m/Y H:i');
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($datetime)
    {
        dump($datetime);
        
        // datetime optional
        if (!$datetime) {
            return;
        }

        return date_create_from_format('d/m/Y H:i', $datetime);
    }

}