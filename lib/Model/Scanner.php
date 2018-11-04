<?php

namespace Phpactor\Pacman\Model;

use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Phpactor\Filesystem\Domain\Filesystem;

class Scanner
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Collector[]
     */
    private $collectors = [];

    /**
     * @var RepositoryInterface
     */
    private $repository;

    public function __construct(RepositoryInterface $repository, Filesystem $filesystem, array $collectors)
    {
        $this->filesystem = $filesystem;
        $this->collectors = $collectors;
        $this->repository = $repository;
    }

    public function scan(string $pattern): array
    {
        $packages = array_filter(array_map(function (array $data) {
            return $this->repository->findPackage($data['name'], '*');
        }, $this->repository->search($pattern, '*')));

        $packages = array_map(function (PackageInterface $package) {
            return PackageMetrics::fromPackage($package);
        }, $packages);

        foreach ($this->filesystem->fileList() as $file) {
            foreach ($packages as $packageMetrics) {
                foreach ($this->collectors as $collector) {
                    $collector->collect($packageMetrics, $file);
                }
            }
        }

        return $packages;
    }
}
