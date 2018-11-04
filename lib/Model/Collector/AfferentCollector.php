<?php

namespace Phpactor\Pacman\Model\Collector;

use Phpactor\Pacman\Model\Collector;
use Phpactor\Pacman\Model\PackageMetrics;
use Phpactor\Pacman\Model\Reference;
use Phpactor\WorseReflection\Core\ClassName;
use Phpactor\WorseReflection\Core\Name;
use Phpactor\WorseReflection\Core\SourceCode;
use Phpactor\WorseReflection\Reflector;

class AfferentCollector implements Collector
{
    /**
     * @var Reflector
     */
    private $reflector;
 
    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }

    public function collect(PackageMetrics $packageMetrics, string $file)
    {
        $source = file_get_contents($file);
        $namespaces = $packageMetrics->namespaces();
        foreach ($namespaces as $namespace) {
            if (false === strpos($source, $namespace)) {
                return;
            }
        }

        $classReflections = $this->reflector->reflectClassesIn(SourceCode::fromPathAndString($file, $source));
        foreach ($classReflections as $classReflection) {
            foreach ($classReflection->scope()->nameImports() as $nameImport) {
                foreach ($namespaces as $namespace) {
                    if ($this->refersToPackage($namespace, $classReflection->name(), $nameImport)) {
                        $packageMetrics->addAfferent(new Reference(
                            $classReflection->name()->full(),
                            $classReflection->sourceCode()->path()
                        ));
                    }
                }
            }
        }
    }

    private function refersToPackage(string $packageNamespace, ClassName $className, Name $nameImport)
    {
        if (0 === strpos($className->full(), $packageNamespace)) {
            return false;
        }

        if (0 === strpos($nameImport->full(), $packageNamespace)) {
            return true;
        }

        return false;
    }
}
