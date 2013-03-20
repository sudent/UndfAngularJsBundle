<?php
namespace Undf\AngularJsBundle\Assetic;

use Assetic\Asset\AssetInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Undf\AngularJsBundle\ConfigManager;

/**
 * AngularJs filter.
 *
 * @author Dani Gonzalez <daniel.gonzalez@undefined.es>
 */
class AngularJsFilter implements \Assetic\Filter\FilterInterface
{

    private $configManager;
    private $catalogue;
    private $kernel;
    private $parameterBag;

    public function __construct(ConfigManager $configManager, array $catalogue, KernelInterface $kernel, ParameterBagInterface $parameterBag)
    {
        $this->configManager = $configManager;
        $this->catalogue = $catalogue;
        $this->kernel = $kernel;
        $this->parameterBag = $parameterBag;
    }

    public function filterLoad(AssetInterface $asset)
    {

    }

    public function filterDump(AssetInterface $asset)
    {
        if($configName = $this->configManager->getConfigNameFromMasterFile($asset->getSourcePath())) {
            $includedModules = $this->configManager->getConfig($configName);

            //Make sure that the AngularJs library is included in the first place.
            $content = $this->getFileContent($this->getAngularJsFile());

            $aliases = array();
            foreach ($includedModules as $parent => $modules) {
                foreach ($modules as $alias) {
                    //AngularJs library itself should not be included here as a module
                    if ($alias != 'angularjs' && array_key_exists($alias, $this->catalogue[$parent])) {
                        $aliases[] = $alias;
                        foreach ($this->catalogue[$parent][$alias]['files'] as $filename) {
                            if (strrchr($filename, '.') == '.js')
                                $content .= $this->getFileContent($filename);
                        }
                    }
                }
            }
            $content .= $this->getModuleDeclaration($configName, $aliases);
            $asset->setContent($content);
        }
    }

    private function getAngularJsFile()
    {
        return $this->catalogue['vendors']['angularjs']['files'][0];
    }

    private function getFileContent($filename)
    {
        if (false === $content = file_get_contents($this->parseAssetRoot($filename))) {
            throw new \Exception(sprintf('File "%s" not found.', $filename));
        }
        return $content;
    }

    /**
     * Adds support for bundle notation file and glob assets and parameter placeholders.
     *
     * FIXME: This is a naive implementation of globs in that it doesn't
     * attempt to support bundle inheritance within the glob pattern itself.
     */
    private function parseAssetRoot($path)
    {
        $path = $this->parameterBag->resolveValue($path);

        // expand bundle notation
        if ('@' == $path[0] && false !== strpos($path, '/')) {
            // use the bundle path as this asset's root
            $bundle = substr($path, 1);
            if (false !== $pos = strpos($bundle, '/')) {
                $bundle = substr($bundle, 0, $pos);
            }
            $options['root'] = array($this->kernel->getBundle($bundle)->getPath());

            // canonicalize the input
            if (false !== $pos = strpos($path, '*')) {
                // locateResource() does not support globs so we provide a naive implementation here
                list($before, $after) = explode('*', $path, 2);
                $path = $this->kernel->locateResource($before) . '*' . $after;
            } else {
                $path = $this->kernel->locateResource($path);
            }
        } else {
            // Direct path to web
            $path = $this->kernel->getRootDir() . '/../web/' . $path;
        }
        return $path;
    }

    private function getModuleDeclaration($configName, $modules)
    {
        $name = 'undf'.  ucfirst($configName).'Module';
        return sprintf('%sangular.module("%s",["%s"])', PHP_EOL . PHP_EOL, $name, implode('","', $modules));
    }

}
