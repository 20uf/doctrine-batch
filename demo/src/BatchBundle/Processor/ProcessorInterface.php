<?php

namespace BatchBundle\Processor;

/**
 * Processor interface
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
interface ProcessorInterface
{
    public function process($item);
}
