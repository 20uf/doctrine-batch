<?php

namespace BatchBundle\Reader;

use BeerBundle\Entity\Beer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class BeerReader implements ReaderInterface
{
    /** @var EntityRepository */
    private $repository;

    /** @var \ArrayIterator */
    private $results;

    /** @var int */
    private $lastId = 0;

    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository('BeerBundle\Entity\Beer');
    }

    public function read()
    {
        if (null === $this->results || empty($this->results)) {
            $this->repository->clear();
            $this->getResults($this->lastId);
        }
        if (empty($this->results)) {
            return null;
        }

        $result = array_shift($this->results);
        $this->lastId = $result->getId();

        return $result;
    }

    private function getResults($id = 0)
    {
        $qb = $this->repository->createQueryBuilder('b');
        $qb
            ->where($qb->expr()->gt('b.id', ':id'))
            ->orderBy('b.id')
            ->setMaxResults(100)
            ->setParameter('id', $id);

        $this->results = $qb->getQuery()->execute();
    }
}
