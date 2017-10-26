<?php

namespace FixtureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class BreweryGeneratorCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('demo:fixture-generator:brewery')
            ->addArgument('filepath', InputArgument::REQUIRED)
            ->addArgument('count', InputArgument::REQUIRED)
        ;
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
        $breweryCodes = [];

        $headers = [
            'code',
            'name',
            'description',
            'address',
            'city',
            'country',
            'phone'
        ];

        $fd = fopen($outputBrewery, 'a+');
        fputcsv($fd, $headers, ';');
        while ($count-- > 0) {

            $code = $faker->slug(2, true);
            while (isset($breweryCodes[$code])) {
                $code = $faker->slug(2, true);
            }
            $breweryCodes[$code] = $code;

            $brewery = [
                $code,
                $faker->company,
                $faker->realText(rand(100, 450)),
                $faker->streetAddress,
                $faker->city,
                $faker->country,
                $faker->phoneNumber
            ];
            fputcsv($fd, $brewery, ';');
        }

        fclose($fd);
    }
}
