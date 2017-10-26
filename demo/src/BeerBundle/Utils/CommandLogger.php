<?php

namespace BeerBundle\Utils;

use Symfony\Component\Console\Output\OutputInterface;


/**
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class CommandLogger
{

    private static $time = [];

    /** @var OutputInterface */
    private static $output;

    static public function setOutput(OutputInterface $output)
    {
        self::$output = $output;
    }

    static public function info($message)
    {
        self::$output->writeln(sprintf('<info>%s</info>', $message));
    }

    static public function error($message)
    {
        self::$output->writeln(sprintf('<error>%s</error>', $message));
    }

    static public function memory($key)
    {
        self::$output->writeln(sprintf('<info>Memory: %sM</info>', Memory::getUsage($key)));
    }

    static public function timeAndMemory($key)
    {
        self::$output->writeln(sprintf(
            '<info>%s - Time: %ss - Memory: %sM - Diff: %sM</info>',
            $key,
            Timer::elapsedTime($key),
            Memory::getUsage(),
            Memory::getDiff($key)
        ));
    }
}
