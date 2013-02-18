<?php

namespace Undf\AngularJsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * 
 * @author Dani Gonzalez <daniel.gonzalez@undefined.es>
 */
class ListCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('undf:angularjs:catalogue')
                ->setDescription('Shows the list of available AngularJs modules.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $catalogue = $this->getContainer()->getParameter('undf_angular_js.catalogue');

        foreach ($catalogue as $parent => $modules) {
            $output->writeln(sprintf('<fg=white>Available modules inside the <options=bold>%s</options=bold> set:</fg=white>', $parent));
            foreach ($modules as $name => $info) {
                $output->writeln(sprintf('  <comment>%s: </comment><info>%s</info>', $name, $info['description']));
            }
        }
    }

}
