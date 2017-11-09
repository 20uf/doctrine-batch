<?php

namespace BatchBundle\Job;

use BatchBundle\Reader\ReaderInterface;
use BatchBundle\Writer\WriterInterface;
use BatchBundle\Processor\ProcessorInterface;
use BeerBundle\Utils\CommandLogger;
use BeerBundle\Utils\Timer;


/**
 * Main job class
 * - Iterates over a reader returning items line per line
 * - Transforms this item
 * - Writes depending of a batch size
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class Job
{
    /** @var ReaderInterface */
    private $reader;

    /** @var ProcessorInterface */
    private $processor;

    /** @var WriterInterface */
    private $writer;

    /** @var int */
    private $batchSize = 100;

    /**
     * @param ReaderInterface $reader
     * @param ProcessorInterface $processor
     * @param WriterInterface $writer
     */
    public function __construct(ReaderInterface $reader, ProcessorInterface $processor, WriterInterface $writer)
    {
        $this->reader = $reader;
        $this->processor = $processor;
        $this->writer = $writer;
    }

    /**
     * Executes a job calling reader, processor and writer
     *
     * Reader and processor handles item one per one
     * Writer handles items with a number depending of the batch size
     */
    public function execute()
    {
        CommandLogger::memory('job');
        Timer::startTime('job');

        $itemsToWrite  = [];
        $writeCount    = 0;

        $stopExecution = false;
        while (!$stopExecution) {
            $readItem = $this->reader->read();
            if (null === $readItem) {
                $stopExecution = true;
                continue;
            }

            $processedItem = $this->processor->process($readItem);

            if (null !== $processedItem) {
                $itemsToWrite[] = $processedItem;
                $writeCount++;
                if (0 === $writeCount % $this->batchSize) {
                    $this->writer->write($itemsToWrite);
                    $itemsToWrite = [];
                }
            }
        }

        if (count($itemsToWrite) > 0) {
            $this->writer->write($itemsToWrite);
        }
        CommandLogger::timeAndMemory('job');
        meminfo_objects_summary(fopen('/tmp/doctrine_batch.log','a+'));
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize(int $batchSize)
    {
        $this->batchSize = $batchSize;
    }
}
