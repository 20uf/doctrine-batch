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

    private $count = 0;

    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        // TODO: Warning: This is a piece of shit but I did not want to make it properly :D
        if (0 === $this->count % 100) {
            $this->count = 0;
            $this->codeSet = [];
        }
        $this->count++;
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