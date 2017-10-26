<?php

namespace BatchBundle\Reader;

/**
 * Iterates over a CSV file and returns it line per line
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class CsvReader implements ReaderInterface
{
    /** @var bool|resource */
    private $fd;

    /** @var string[] */
    private $headers = [];

    public function __construct($filepath)
    {
        $this->fd = fopen($filepath, 'r+');
        $this->headers = fgetcsv($this->fd, null, ';');
    }

    public function read()
    {
        $row = fgetcsv($this->fd, null, ';');
        if (false === $row) {
            return null;
        }

        return array_combine($this->headers, $row);
    }

    public function __destruct()
    {
        fclose($this->fd);
    }
}
