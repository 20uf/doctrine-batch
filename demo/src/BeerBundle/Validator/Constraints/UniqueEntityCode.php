<?php

namespace BeerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 */
class UniqueEntityCode extends Constraint
{
    /** @var string */
    public $message = 'The value "%unique_code%" is already set in another entity';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'BeerBundle\Validator\Constraints\UniqueEntityCodeValidator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}