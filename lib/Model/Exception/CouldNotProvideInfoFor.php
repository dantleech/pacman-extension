<?php

namespace Phpactor\Pacman\Model\Exception;

use Exception;

class CouldNotProvideInfoFor extends Exception
{
    /**
     * @var string
     */
    private $packageName;

    public function __construct(string $packageName)
    {
        parent::__construct(sprintf(
            'Could not provide info for "%s"', $packageName
        ));
        $this->packageName = $packageName;
    }

    public function packageName(): string
    {
        return $this->packageName;
    }
}
