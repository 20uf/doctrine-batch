<?php

namespace BatchBundle\Command;

use BatchBundle\Job\Job;
use BatchBundle\Processor\BeerToArrayProcessor;
use BatchBundle\Reader\BeerReader;
use BatchBundle\Writer\CsvWriter;
use BeerBundle\Utils\CommandLogger;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to export beers
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class ExportBeerCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('batch:export:beer')
            ->addArgument('filepath', InputArgument::REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        CommandLogger::setOutput($output);

        $filepath = $input->getArgument('filepath');
        $headers = ['code', 'name', 'description', 'percent', 'quotation', 'brewery', 'category'];

        $reader = new BeerReader($this->getEntityManager());
        $processor = new BeerToArrayProcessor();
        $writer = new CsvWriter($filepath, $headers);

        $job = new Job($reader, $processor, $writer);
        $job->setBatchSize(100);

        $job->execute();
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @return ObjectRepository
     */
    private function getBeerRepository()
    {
        return $this->getEntityManager()->getRepository('BeerBundle\Entity\Beer');
    }
}
