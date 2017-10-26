<?php

namespace BatchBundle\Writer;


/**
 * @author Romain Monceau <romain@akeneo.com>
 */
interface WriterInterface
{
    public function write(array $items);
}
