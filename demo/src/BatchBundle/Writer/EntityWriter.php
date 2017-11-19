<?php

namespace BatchBundle\Writer;

use BeerBundle\Utils\CommandLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Validates and saves an entity in the database
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class EntityWriter implements WriterInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function write(array $items)
    {
        $writeCount = 0;
        foreach ($items as $item) {
            $violations = $this->validator->validate($item);
            if ($violations->count() === 0) {
                $this->em->persist($item);
                $writeCount++;
            } else {
                foreach ($violations as $violation) {
                    CommandLogger::error(
                        sprintf('Entity "%s" not valid: %s', $item->getCode(), $violation->getMessage())
                    );
                }
            }
        }

        //CommandLogger::info(sprintf('%s entity written', $writeCount));

        $this->em->flush();
        foreach ($items as $item) {
            $this->em->detach($item);
        }
    }
}
