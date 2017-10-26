<?php

namespace BeerBundle\Entity;


/**
 * @author  Romain Monceau <romain@akeneo.com>
 * @license TODO
 */
class Beer
{
    private $id;

    private $code;

    private $name;

    private $description;

    private $percent;

    private $quotation;

    private $brewery;

    private $category;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param mixed $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    }

    /**
     * @return mixed
     */
    public function getQuotation()
    {
        return $this->quotation;
    }

    /**
     * @param mixed $quotation
     */
    public function setQuotation($quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * @return mixed
     */
    public function getBrewery()
    {
        return $this->brewery;
    }

    /**
     * @param mixed $brewery
     */
    public function setBrewery($brewery)
    {
        $this->brewery = $brewery;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}
