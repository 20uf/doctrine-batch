<?php

namespace FixtureBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class BeerGeneratorCommand extends ContainerAwareCommand
{
    /** @var array */
    private $categoryCodes = [];

    /** @var array */
    private $breweryCodes = [];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('demo:fixture-generator:beer')
            ->addArgument('filepath', InputArgument::REQUIRED)
            ->addArgument('count', InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->getRepository('BeerBundle\Entity\Category')->createQueryBuilder('c');
        $this->categoryCodes = $qb->select('c.code')->getQuery()->getScalarResult();

        $qb = $em->getRepository('BeerBundle\Entity\Brewery')->createQueryBuilder('b');
        $this->breweryCodes = $qb->select('b.code')->getQuery()->getScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = (int) $input->getArgument('count');
        $outputBrewery = $input->getArgument('filepath');

        if (!is_int($count)) {
            throw new \Exception(sprintf('Parameter count "%s" is not a number', $count));
        }

        $faker = \Faker\Factory::create('en_US');
        //$beerCodes = [];

        $headers = [
            'code',
            'name',
            'description',
            'percent',
            'quotation',
            'brewery',
            'category'
        ];

        $fd = fopen($outputBrewery, 'a+');
        fputcsv($fd, $headers, ';');
        while ($count-- > 0) {

            $code = $faker->slug(3, true);
            //while (isset($beerCodes[$code])) {
            //   $code = $faker->slug(2, true);
            //}
            //$beerCodes[$code] = $code;

            $beer = [
                $code,
                $faker->company,
                $faker->realText(rand(100, 450)),
                rand(10, 400) / 10,
                rand(0, 500) / 100,
                $this->breweryCodes[rand(0, count($this->breweryCodes) - 1)]['code'],
                $this->categoryCodes[rand(0, count($this->categoryCodes) - 1)]['code']
            ];
            fputcsv($fd, $beer, ';');
        }

        fclose($fd);
    }
}
