<?php

namespace BeerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

/**
 * Implementation for entities
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class EntityRepository extends BaseEntityRepository implements IdentifiableRepositoryInterface
{
    public function findOneByIdentifier(string $code)
    {
        return $this->findOneBy(['code' => $code]);
    }
}
