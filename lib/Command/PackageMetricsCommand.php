<?php

namespace Phpactor\Pacman\Command;

use Phpactor\Pacman\Model\PackageInfoFinder;
use Phpactor\Pacman\Model\PackageMetrics;
use Phpactor\Pacman\Model\PackageMetrics\PackageFinder;
use Phpactor\Pacman\Model\Scanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackageMetricsCommand extends Command
{
    const NAME = 'pattern';

    /**
     * @var Scanner
     */
    private $scanner;

    public function __construct(Scanner $scanner)
    {
        parent::__construct();
        $this->scanner = $scanner;
    }

    protected function configure()
    {
        $this->setDescription('Show metrics for a given package');
        $this->addArgument(self::NAME, InputArgument::REQUIRED, 'Name of package or pattern to match');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Ca = Afferent Couplings:</> Number of classes in other packages referring to this class');
        $output->writeln('<info>Ce = Efferent Couplings:</> Number of classes in package referring to classes in other packages');
        $output->writeln('<info>A = Abstractness:</> Ratio of abstract to concrete classes. 1 is completely abstract');
        $output->writeln('<info>I = Instability:</> Ration of efferent to afferent couplings. 1 is completely unstable package');
        $table = new Table($output);
        $table->setHeaders([
            'name', 'version', 'Ca ☐←', 'Ce ☐→', 'A', 'I',
        ]);
        foreach ($this->scanner->scan($input->getArgument(self::NAME)) as $metrics) {
            $this->addMetricsRow($table, $metrics);
        }
        $table->render();
    }

    private function addMetricsRow(Table $table, PackageMetrics $metrics)
    {
        $table->addRow([
            $metrics->name(),
            $metrics->version(),
            count($metrics->afferentRefs()),
            count($metrics->efferentRefs()),
            number_format($metrics->abstractness(), 2),
            number_format($metrics->instability(), 2)
        ]);
    }
}
