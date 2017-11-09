<?php

namespace BatchBundle\Processor;

use BeerBundle\Entity\Beer;
use BeerBundle\Entity\Repository\IdentifiableRepositoryInterface;


/**
 * Transforms a flat item representing a beer to a beer entity
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class BeerProcessor implements ProcessorInterface
{
    /** @var IdentifiableRepositoryInterface */
    private $beerRepository;

    /** @var IdentifiableRepositoryInterface */
    private $breweryRepository;

    /** @var IdentifiableRepositoryInterface */
    private $categoryRepository;

    /**
     * @param IdentifiableRepositoryInterface $beerRepository
     * @param IdentifiableRepositoryInterface $breweryRepository
     * @param IdentifiableRepositoryInterface $categoryRepository
     */
    public function __construct(
        IdentifiableRepositoryInterface $beerRepository,
        IdentifiableRepositoryInterface $breweryRepository,
        IdentifiableRepositoryInterface $categoryRepository

    ) {
        $this->beerRepository = $beerRepository;
        $this->breweryRepository = $breweryRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function process($item)
    {
        $beer = $this->findOrCreateBeer($item['code']);
        $beer->setName($item['name']);
        $beer->setDescription($item['description']);
        $beer->setPercent($item['percent']);
        $beer->setQuotation($item['quotation']);

        $category = $this->categoryRepository->findOneByIdentifier($item['category']);
        $beer->setCategory($category);

        $brewery = $this->breweryRepository->findOneByIdentifier($item['brewery']);
        $beer->setBrewery($brewery);

        return $beer;
    }

    public function findOrCreateBeer(string $code)
    {
        $entity = $this->beerRepository->findOneByIdentifier($code);
        if (null === $entity) {
            $entity = new Beer();
            $entity->setCode($code);
        }

        return $entity;
    }
}
