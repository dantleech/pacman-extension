<?php

namespace Phpactor\Pacman\Model\Collector;

use Phpactor\Pacman\Model\Collector;
use Phpactor\Pacman\Model\PackageMetrics;
use Phpactor\Pacman\Model\Reference;
use Phpactor\WorseReflection\Core\Reflection\ReflectionClass;
use Phpactor\WorseReflection\Core\Reflection\ReflectionInterface;
use Phpactor\WorseReflection\Core\Reflection\ReflectionTrait;
use Phpactor\WorseReflection\Core\SourceCode;
use Phpactor\WorseReflection\Reflector;

class AbsractnessCollector implements Collector
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

        if (false === strpos($file, $packageMetrics->name())) {
            return;
        }

        $classReflections = $this->reflector->reflectClassesIn(SourceCode::fromPathAndString($file, $source));

        foreach ($classReflections as $classReflection) {
            foreach ($classReflection->scope()->nameImports() as $nameImport) {
                foreach ($packageMetrics->namespaces() as $namespace) {
                    if (0 === strpos($namespace, $nameImport->full())) {
                        continue;
                    }

                    if ($classReflection instanceof ReflectionInterface || $classReflection instanceof ReflectionClass && $classReflection->isAbstract()) {
                        $packageMetrics->addAbstract($classReflection->name()->full());
                        continue;
                    }

                    if ($classReflection instanceof ReflectionClass || $classReflection instanceof ReflectionTrait) {
                        $packageMetrics->addConcrete($classReflection->name()->full());
                    }
                }
            }
        }

    }
}
