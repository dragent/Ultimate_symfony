<?php

namespace app\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransfosmer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value === null)
            return null;
        return $value / 100;
    }

    public function reverseTransform($value)
    {
        if ($value === null)
            return null;
        return $value * 100;
    }
}
