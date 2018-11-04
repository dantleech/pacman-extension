<?php

namespace Phpactor\Pacman\Model;

class Reference
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $className, string $filePath)
    {
        $this->className = $className;
        $this->filePath = $filePath;
    }
}
