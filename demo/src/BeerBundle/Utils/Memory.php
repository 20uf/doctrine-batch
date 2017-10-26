<?php

namespace BeerBundle\Utils;


/**
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
final class Memory
{
    static private $memory = [];

    static public function getUsage($key = null)
    {
        if (null === $key) {
            return self::getMemory();
        }

        self::$memory[$key] = self::getMemory();

        return self::$memory[$key];
    }

    static public function getDiff($key)
    {
        return self::getMemory() - self::$memory[$key];
    }

    static private function getMemory()
    {
        return round(memory_get_usage(true) / 1000 / 1000, 2);
    }
}
