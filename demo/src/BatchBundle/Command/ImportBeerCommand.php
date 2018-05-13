<?php

namespace BatchBundle\Command;

use BatchBundle\Job\Job;
use BatchBundle\Processor\BeerProcessor;
use BatchBundle\Reader\CsvReader;
use BatchBundle\Writer\EntityWriter;
use BeerBundle\Entity\Beer;
use BeerBundle\Entity\Repository\IdentifiableRepositoryInterface;
use BeerBundle\Utils\CommandLogger;
use BeerBundle\Utils\Timer;
use BeerBundle\Validator\Constraints\UniqueEntityCodeValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command to import beer
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class ImportBeerCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('batch:import:beer')
            ->addArgument('filepath', InputArgument::REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        CommandLogger::setOutput($output);

        $filepath = $input->getArgument('filepath');
        if (!file_exists($filepath)) {
            throw new \Exception(sprintf('File "%s" not found', $filepath));
        }

        CommandLogger::memory('Import');
        Timer::startTime('Import');

        // Read content
        $writeCount = 0;
        $fd = fopen($filepath, 'r+');
        $headers = fgetcsv($fd, null, ';');

        while ($csvRow = fgetcsv($fd, null, ';')) {
            $csvRow = array_combine($headers, $csvRow);

            // Process data row
            $entity = $this->process($csvRow);

            // Validate entity
            $violations = $this->getValidator()->validate($entity);
            if ($violations->count() === 0) {
                // Write entity
                $this->getEntityManager()->persist($entity);
                if (0 === ++$writeCount % 100) {
                    $this->getEntityManager()->flush();
                    $this->getEntityManager()->clear();
                    UniqueEntityCodeValidator::reset();
                }
            } else {
                $this->printViolations($violations, $entity);
            }
        }
        CommandLogger::info(sprintf('%s entity written', $writeCount));

        fclose($fd);

        gc_collect_cycles();
        meminfo_info_dump(fopen('/tmp/doctrine_batch.log','w'));
        CommandLogger::timeAndMemory('Import');
    }

    private function process(array $item)
    {
        $beer = $this->findOrCreateBeer($item['code']);
        $beer->setName($item['name']);
        $beer->setDescription($item['description']);
        $beer->setPercent($item['percent']);
        $beer->setQuotation($item['quotation']);

        $category = $this->getCategoryRepository()->findOneByIdentifier($item['category']);
        $beer->setCategory($category);

        $brewery = $this->getBreweryRepository()->findOneByIdentifier($item['brewery']);
        $beer->setBrewery($brewery);

        return $beer;
    }

    private function findOrCreateBeer(string $code)
    {
        $entity = $this->getBeerRepository()->findOneByIdentifier($code);
        if (null === $entity) {
            $entity = new Beer();
            $entity->setCode($code);

        }

        return $entity;
    }

    private function printViolations(ConstraintViolationList $violations, $item)
    {
        foreach ($violations as $violation) {
            CommandLogger::error(
                sprintf('Entity "%s" not valid: %s', $item->getCode(), $violation->getMessage())
            );
        }
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidator()
    {
        return $this->getContainer()->get('validator');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @return IdentifiableRepositoryInterface
     */
    private function getBeerRepository()
    {
        return $this->getEntityManager()->getRepository('BeerBundle\Entity\Beer');
    }

    private function getBreweryRepository()
    {
        return $this->getEntityManager()->getRepository('BeerBundle\Entity\Brewery');
    }

    private function getCategoryRepository()
    {
        return $this->getEntityManager()->getRepository('BeerBundle\Entity\Category');
    }

}
