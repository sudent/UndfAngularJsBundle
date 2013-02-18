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
class CreateMasterFilesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('undf:angularjs:create-master-files')
                ->setDescription('Creates a master file for every module set declared in the UndfAngularJsBundle configuration.')
                ->addArgument('root-folder', InputArgument::REQUIRED, 'Path to the folder where the master files will be created.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = $this->validateRoot($input->getArgument('root-folder'));
        $configManager = $this->getContainer()->get('undf.angular_js.config_manager');

        foreach (array_keys($configManager->getConfigs()) as $configname) {
            $filename = $configManager->getMasterFileForConfig($configname);
            $filename = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

            if (false === file_put_contents($filename, $this->generateMasterFileText($configname))) {
                throw new \Exception(sprintf('Unable to create file "%s"', $filename));
            }
            $output->writeln(sprintf('<comment>%s</comment> <info>[file+]</info> %s', date('H:i:s'), $filename));
        }

        $output->writeln('');
        $output->writeln('<info>You can now include one or more of these files to the javascripts section of your templates.</info>');
        $output->writeln('<comment>Do NOT forget applying the "angularjs" filter to the master files,</comment>');
        $output->writeln('<comment>so assetic can take care of dumping the right content into the output files.</comment>');
    }

    protected function validateRoot($root)
    {
        if ('@' === $root[0]) {
            if (false === $pos = strpos($root, '/')) {
                $bundleName = substr($root, 1);
            } else {
                $bundleName = substr($root, 1, $pos - 1);
            }

            $bundles = $this->getContainer()->getParameter('kernel.bundles');
            if (!isset($bundles[$bundleName])) {
                throw new \Exception(sprintf('The bundle "%s" does not exist. Available bundles: %s', $bundleName, implode(', ', array_keys($bundles))));
            }

            $ref = new \ReflectionClass($bundles[$bundleName]);
            $root = false === $pos ? dirname($ref->getFileName()) : dirname($ref->getFileName()) . substr($root, $pos);
        }

        if (!is_dir($root)) {
            throw new \Exception('The directory "%s" does not exist.');
        }
        return $root;
    }

    protected function generateMasterFileText($configname)
    {
        $text = "//This is the master file for the '$configname' config." . PHP_EOL;
        $text .= '//Check your assetic configuration to make sure that this file is dumped with the' . PHP_EOL;
        $text .= '//"angularjs" filter in order to get all modules set in the config within the output file' . PHP_EOL;
        return $text;
    }

}
