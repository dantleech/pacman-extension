<?php

namespace Phpactor\Pacman\Model;

class PackageInfo
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var array
     */
    private $info;

    public function __construct(string $packageName, array $info)
    {
        $this->packageName = $packageName;
        $this->info = $info;
    }

    public function info(): array
    {
        return $this->info;
    }
}
