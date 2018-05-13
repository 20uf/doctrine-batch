<?php

namespace BatchBundle\Command;
;
use BeerBundle\Entity\Beer;
use BeerBundle\Utils\CommandLogger;
use BeerBundle\Utils\Timer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        CommandLogger::memory('Export');
        Timer::startTime('Export');

        $filepath = $input->getArgument('filepath');
        $fd = fopen($filepath, 'a+');
        fputcsv($fd, ['code', 'name', 'description', 'percent', 'quotation', 'brewery', 'category'], ';');

        $beers = $this->getBeerRepository()->findAll();
        foreach ($beers as $beer) {
            $csvRow = $this->process($beer);
            fputcsv($fd, $csvRow, ';');
        }

        fclose($fd);
        CommandLogger::timeAndMemory('Export');

        gc_collect_cycles();
        meminfo_info_dump(fopen('/tmp/doctrine_batch.log','w'));
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

    private function process(Beer $beer)
    {
        $data = [];
        $data['code'] = $beer->getCode();
        $data['name'] = $beer->getName();
        $data['description'] = $beer->getDescription();
        $data['percent'] = $beer->getPercent();
        $data['quotation'] = $beer->getQuotation();
        $data['brewery'] = $beer->getBrewery()->getCode();
        $data['category'] = $beer->getCategory()->getCode();

        return $data;
    }
}
