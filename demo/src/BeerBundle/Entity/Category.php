<?php

namespace BeerBundle\Entity;


/**
 * @author  Romain Monceau <romain@akeneo.com>
 * @license TODO
 */
class Category
{
    private $id;

    private $code;

    private $name;

    private $description;

    public function getCode()
    {
        return $this->code;
    }

    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }
}
