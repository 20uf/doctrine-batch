<?php

namespace BatchBundle\Command;

use BatchBundle\Job\Job;
use BatchBundle\Processor\CategoryProcessor;
use BatchBundle\Reader\CsvReader;
use BatchBundle\Writer\EntityWriter;
use BeerBundle\Entity\Category;
use BeerBundle\Entity\Repository\IdentifiableRepositoryInterface;
use BeerBundle\Utils\CommandLogger;
use BeerBundle\Utils\Timer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command to import categories
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class ImportCategoryCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('batch:import:category')
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
                $this->getEntityManager()->flush();
                $writeCount++;
            } else {
                $this->printViolations($violations, $entity);
            }
        }
        CommandLogger::info(sprintf('%s entity written', $writeCount));

        fclose($fd);
        CommandLogger::timeAndMemory('Import');
    }

    private function printViolations(ConstraintViolationList $violations, $item)
    {
        foreach ($violations as $violation) {
            CommandLogger::error(
                sprintf('Entity "%s" not valid: %s', $item->getCode(), $violation->getMessage())
            );
        }
    }

    private function process(array $item)
    {
        $category = $this->findOrCreateCategory($item['code']);
        $category->setName($item['name']);
        $category->setDescription($item['description']);

        return $category;
    }

    private function findOrCreateCategory(string $code)
    {
        $entity = $this->getRepository()->findOneByIdentifier($code);
        if (null === $entity) {
            $entity = new Category();
            $entity->setCode($code);
        }

        return $entity;
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
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('BeerBundle\Entity\Category');
    }

}
