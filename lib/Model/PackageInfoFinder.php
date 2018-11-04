<?php

namespace Phpactor\Pacman\Model;

use Composer\Repository\RepositoryInterface;
use Generator;

class PackageInfoFinder
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var InfoProvider[]
     */
    private $providers;

    public function __construct(RepositoryInterface $repository, array $providers)
    {
        $this->repository = $repository;
        $this->providers = $providers;
    }

    /**
     * @return Generator<PackageInfo>
     */
    public function find(string $search): Generator
    {
        foreach ($this->repository->search($search) as $packageData) {
            $package = $this->repository->findPackage($packageData['name'], '*');

            $info = [];
            foreach ($this->providers as $provider) {
                $info = array_merge($info, $provider->provide($package));
            }

            yield new PackageInfo($package->getName(), $info);
        }
    }
}
