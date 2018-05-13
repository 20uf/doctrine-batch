<?php

namespace BeerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class UniqueEntityCodeValidator extends ConstraintValidator
{
    /** @var string[] */
    static private $codeSet = [];

    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        if (isset(static::$codeSet[$entity->getCode()])) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('%unique_code%', $entity->getCode())
                ->addViolation();

            return;
        }
        static::$codeSet[$entity->getCode()] = $entity->getCode();
    }

    static public function reset()
    {
        static::$codeSet = [];
    }
}
