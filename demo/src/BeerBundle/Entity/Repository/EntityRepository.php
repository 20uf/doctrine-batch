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
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere(
            $qb->expr()->like('c.code', $qb->expr()->literal($code))
        );


        $results = $qb->getQuery()->execute();

        $count = count($results);

        if ($count === 0) {
            return null;
        }

        if ($count > 1) {
            throw new \Exception('This query should return only one element');
        }

        return $results[0];
    }
}
