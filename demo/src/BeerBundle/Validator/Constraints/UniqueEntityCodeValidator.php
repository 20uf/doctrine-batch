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
    private $codeSet = [];

    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        if (isset($this->codeSet[$entity->getCode()])) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('%unique_code%', $entity->getCode())
                ->addViolation();

            return;
        }
        $this->codeSet[$entity->getCode()] = $entity->getCode();
    }
}