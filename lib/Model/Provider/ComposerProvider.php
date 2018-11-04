<?php

namespace Phpactor\Pacman\Model\Provider;

use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\WritableRepositoryInterface;
use Phpactor\Pacman\Model\InfoProvider;
use Phpactor\Pacman\Model\PackageName;

class ComposerProvider implements InfoProvider
{
    public function provide(PackageInterface $package): array
    {
        return [
            'name' => $package->getName(),
            'version' => $package->getPrettyVersion(),
        ];
    }
}
