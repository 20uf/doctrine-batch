<?php

namespace BatchBundle\Processor;

use BeerBundle\Entity\Category;
use BeerBundle\Entity\Repository\IdentifiableRepositoryInterface;


/**
 * Transforms a flat item representing a category to a category entity
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class CategoryProcessor implements ProcessorInterface
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
        $category = $this->findOrCreateCategory($item['code']);
        $category->setName($item['name']);
        $category->setDescription($item['description']);

        return $category;
    }

    public function findOrCreateCategory(string $code)
    {
        $entity = $this->repository->findOneByIdentifier($code);
        if (null === $entity) {
            $entity = new Category();
            $entity->setCode($code);
        }

        return $entity;
    }
}
