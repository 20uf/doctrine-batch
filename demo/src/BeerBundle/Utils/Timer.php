<?php

namespace BeerBundle\Utils;


/**
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
final class Timer
{
    static private $timer = [];


    static public function startTime($key)
    {
        self::$timer[$key] = microtime(true);
    }

    static public function elapsedTime($key)
    {
        $startTime = self::$timer[$key];
        $endTime = microtime(true);
        unset(self::$timer[$key]);

        return round($endTime - $startTime, 2);
    }
}
