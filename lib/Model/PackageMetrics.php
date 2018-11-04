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

    private $abstracts = [];
    private $concretes = [];


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
        $this->afferentRefs[$reference->className()] = $reference;
    }

    public function addEfferent(Reference $reference)
    {
        $this->efferentRefs[$reference->className()] = $reference;
    }

    public function namespaces()
    {
        return array_filter(array_reduce($this->package->getAutoload(), function (array $carry, array $autoload) {
            return array_merge($carry, array_keys($autoload));
        }, []));
    }

    public function afferentRefs()
    {
        return $this->afferentRefs;
    }

    public function efferentRefs()
    {
        return $this->efferentRefs;
    }

    public function addAbstract(string $string)
    {
        $this->abstracts[] = $string;
    }

    public function addConcrete(string $string)
    {
        $this->concretes[] = $string;
    }

    public function abstracts()
    {
        return $this->abstracts;
    }

    public function concretes()
    {
        return $this->concretes;
    }

    public function abstractness()
    {
        $abstracts = count($this->abstracts);
        $concretes = count($this->concretes);

        if ($abstracts === 0 && $concretes === 0) {
            return 0;
        }

        $total = 1 / ($abstracts + $concretes);

        return count($this->abstracts) * $total;
    }

    public function instability()
    {
        $efferents = count($this->efferentRefs);
        $afferents = count($this->afferentRefs);

        if ($afferents === 0 && $efferents === 0) {
            return 0;
        }

        $total = 1 / ($afferents + $efferents);

        return $efferents * $total;
    }
}
