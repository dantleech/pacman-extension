<?php

namespace Phpactor\Pacman\Model;

use Composer\Package\PackageInterface;

class PackageMetrics
{
    /**
     * @var PackageInterface
     */
    private $package;

    private $afferentRefs = [];

    private $efferentRefs = [];

    private function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    public function name(): string
    {
        return $this->package->getName();
    }

    public function version(): string
    {
        return $this->package->getFullPrettyVersion();
    }

    public function fromPackage(PackageInterface $packageInterface)
    {
        return new self($packageInterface);
    }

    public function addAfferent(Reference $reference)
    {
        $this->afferentRefs[] = $reference;
    }

    public function addEfferent(Reference $reference)
    {
        $this->efferentRefs[] = $reference;
    }

    public function namespaces()
    {
        return array_reduce($this->package->getAutoload(), function (array $carry, array $autoload) {
            return array_merge($carry, array_keys($autoload));
        }, []);
    }

    public function afferentRefs()
    {
        return $this->afferentRefs;
    }

    public function efferentRefs()
    {
        return $this->efferentRefs;
    }
}
