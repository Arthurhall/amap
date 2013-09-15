<?php

namespace Amap\BlogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Amap\MainBundle\Entity\Image;

class CleanImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('amap:clean-image')
            ->setDescription('Remove orphans images')
            // ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('exec', null, InputOption::VALUE_NONE, 'Remove orphans')
            ->addOption('unlink-entities', null, InputOption::VALUE_NONE, 'Remove orphans entity')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $entities = $em->getRepository('AmapMainBundle:Image')->findAllPath();
        $entity = new Image();
        $uploadDir = $entity->getUploadRootDir();
        
        $images = array_diff(scandir($uploadDir), array('.', '..'));
        
        $output->writeln('Scan Dir return :');
        print_r($images);    
        
        $orphans = array();
        foreach ($images as $key => $img) 
        {
            if(in_array($img, $entities, true)) {
                $output->writeln( 'match: '.$img );
            }
            else {
                $orphans[] = $uploadDir.$img;
                
                $output->writeln( 'delete: '.$img );
            }
        }
        
        $output->writeln('Orphans :');
        print_r($orphans);
        
        if($input->getOption('exec'))
        {
            foreach($orphans as $k => $val) 
            {
                if(file_exists($val))
                    unlink($val);
            }
        }
        
        // Supprime les images qui ne sont reliées à rien :
        if($input->getOption('unlink-entities'))
        {
            $output->writeln('Unlink entities :');
            
            $entities = $em->getRepository('AmapMainBundle:Image')->findAll();
            foreach ($entities as $key => $entity) 
            {
                if(
                    count($entity->getArticle())==0 && 
                    count($entity->getDelivery())==0 && 
                    count($entity->getProductVegetable())==0 )
                {
                    $em->remove($entity);
                    
                    $output->writeln($entity->getPath());
                }
            }
            
            $em->flush();
        }
    }
}