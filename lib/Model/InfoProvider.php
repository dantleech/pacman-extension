<?php

namespace Phpactor\Pacman\Model;

use Composer\Package\PackageInterface;

interface InfoProvider
{
    public function provide(PackageInterface $package): array;
}
