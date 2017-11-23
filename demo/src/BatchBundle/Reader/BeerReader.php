<?php

namespace BatchBundle\Reader;

use BeerBundle\Entity\Beer;
use Doctrine\Common\Persistence\ObjectRepository;
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

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function read()
    {
        if (null === $this->results) {
            $this->results = $this->getResults();
        }

        if (null !== $result = $this->results->current()) {
            $this->results->next();
        }

        return $result;
    }

    private function getResults()
    {
        $results = $this->repository->findAll();
        $this->repository->clear();

        return new \ArrayIterator($results);
    }
}
