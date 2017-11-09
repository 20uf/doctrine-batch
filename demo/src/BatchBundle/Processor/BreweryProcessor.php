<?php

namespace BatchBundle\Processor;

use BeerBundle\Entity\Brewery;
use BeerBundle\Entity\Repository\IdentifiableRepositoryInterface;


/**
 * Transforms a flat item representing a brewery to a brewery entity
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class BreweryProcessor implements ProcessorInterface
{
    /** @var IdentifiableRepositoryInterface */
    private $repository;

    /**
     * @param IdentifiableRepositoryInterface $repository
     */
    public function __construct(IdentifiableRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function process($item)
    {
        $brewery = $this->findOrCreateBrewery($item['code']);
        $brewery->setName($item['name']);
        $brewery->setDescription($item['description']);
        $brewery->setAddress($item['address']);
        $brewery->setCity($item['city']);
        $brewery->setCountry($item['country']);
        $brewery->setPhone($item['phone']);

        return $brewery;
    }

    public function findOrCreateBrewery(string $code)
    {
        $entity = $this->repository->findOneByIdentifier($code);
        if (null === $entity) {
            $entity = new Brewery();
            $entity->setCode($code);
        }

        return $entity;
    }
}
