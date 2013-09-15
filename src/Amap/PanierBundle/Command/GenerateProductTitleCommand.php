<?php

namespace Amap\PanierBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProductTitleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('amap:generate-product-title')
            ->setDescription('Generate products title')
            // ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            // ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $products = $em->getRepository('AmapMainBundle:Product')->findAll();
        
        foreach ($products as $key => $prod) {
            $prod->setTitleValue();
            $output->writeln( $prod->getTitle() );
        }
        
        $em->flush();
    }
}