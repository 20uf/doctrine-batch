<?php

namespace BeerBundle\Entity\Repository;


/**
 * Repository interface to find entity from its code
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
interface IdentifiableRepositoryInterface
{
    public function findOneByIdentifier(string $code);
}
