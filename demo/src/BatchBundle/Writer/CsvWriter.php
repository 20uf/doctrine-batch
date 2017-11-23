<?php

namespace BatchBundle\Writer;


/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class CsvWriter implements WriterInterface
{
    /** @var bool|resource */
    private $fd;

    public function __construct($filepath, $headers)
    {
        $this->fd = fopen($filepath, 'a+');
        fputcsv($this->fd, $headers);
    }

    public function write(array $items)
    {
        foreach ($items as $item) {
            fputcsv($this->fd, $item, ';');
        }
    }

    public function __destruct()
    {
        fclose($this->fd);
    }
}
