<?php

namespace Phpactor\Pacman\Command;

use Phpactor\Pacman\Model\PackageInfoFinder;
use Phpactor\Pacman\Model\PackageMetrics\PackageFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackageMetricsCommand extends Command
{
    const NAME = 'pattern';

    /**
     * @var PackageInfoFinder
     */
    private $packageInfoFinder;

    public function __construct(PackageInfoFinder $packageInfoFinder)
    {
        parent::__construct();
        $this->packageInfoFinder = $packageInfoFinder;
    }

    protected function configure()
    {
        $this->setDescription('Show metrics for a given package');
        $this->addArgument(self::NAME, InputArgument::REQUIRED, 'Name of package or pattern to match');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        foreach ($this->packageInfoFinder->find($input->getArgument(self::NAME)) as $package) {
            $table->addRow($package->info());
        }
        $table->render();
    }
}
