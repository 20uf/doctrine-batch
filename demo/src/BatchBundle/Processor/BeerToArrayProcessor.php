<?php

namespace BatchBundle\Processor;


/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class BeerToArrayProcessor implements ProcessorInterface
{
    public function process($item)
    {
        $data = [];
        $data['code'] = $item->getCode();
        $data['name'] = $item->getName();
        $data['description'] = $item->getDescription();
        $data['percent'] = $item->getPercent();
        $data['quotation'] = $item->getQuotation();
        //$data['brewery'] = $item->getBrewery()->getCode();
        //$data['category'] = $item->getCategory()->getCode();

        return $data;
    }
}
