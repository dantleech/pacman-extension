<?php

namespace Phpactor\Pacman\Model;

interface Collector
{
    public function collect(PackageMetrics $packageMetrics, string $file);
}
