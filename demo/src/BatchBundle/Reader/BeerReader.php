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

    /** @var Beer[] */
    private $results;

    /** @var int */
    private $readCount = 0;

    public function __construct(EntityManager $em)
    {
        $em->getConnection()
            ->getWrappedConnection()
            ->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $this->repository = $em->getRepository('BeerBundle\Entity\Beer');
    }

    public function read()
    {
        if (null === $this->results) {
            $this->results = $this->getResults();
        }

        $result = $this->results->next();
        if (null !== $result) {
            if (++$this->readCount % 100 === 0) {
                $this->repository->clear();
            }
            return $result[0];
        }
    }

    private function getResults()
    {
        $qb = $this->repository->createQueryBuilder('b');

        return $qb->getQuery()->iterate();
    }
}
