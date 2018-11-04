<?php

namespace Phpactor\Pacman;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\SourceCodeFilesystem\SourceCodeFilesystemExtension;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\MapResolver\Resolver;
use Phpactor\Pacman\Command\PackageMetricsCommand;
use Phpactor\Pacman\Model\Collector\AbsractnessCollector;
use Phpactor\Pacman\Model\Collector\AfferentCollector;
use Phpactor\Pacman\Model\Collector\EfferentCollector;
use Phpactor\Pacman\Model\PackageInfoFinder;
use Phpactor\Pacman\Model\PackageMetrics\PackageFinder;
use Phpactor\Pacman\Model\Provider\ComposerProvider;
use Phpactor\Pacman\Model\Scanner;

class PacmanExtension implements Extension
{
    const TAG_INFO_PROVIDER = 'info_provider';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container)
    {
        $container->register('pacman.command.package_metrics', function (Container $container) {
            return new PackageMetricsCommand($container->get('pacman.scanner'));
        }, [ ConsoleExtension::TAG_COMMAND => [ 'name' => 'package:metrics' ] ]);

        $container->register('pacman.package_info_finder', function (Container $container) {
            $providers = [];
            foreach ($container->getServiceIdsForTag(self::TAG_INFO_PROVIDER) as $serviceId => $attrs) {
                $providers[] = $container->get($serviceId);
            }

            return new PackageInfoFinder($container->get('pacman.composer.local_repo'), $providers);
        });

        $container->register('pacman.info_provider.composer', function (Container $container) {
            return new ComposerProvider();
        }, [ self::TAG_INFO_PROVIDER => [] ]);

        $container->register('pacman.scanner', function (Container $container) {
            return new Scanner(
                $container->get('pacman.composer.local_repo'),
                $container->get(SourceCodeFilesystemExtension::SERVICE_FILESYSTEM_COMPOSER),
                [
                    new AfferentCollector($container->get(WorseReflectionExtension::SERVICE_REFLECTOR)),
                    new EfferentCollector($container->get(WorseReflectionExtension::SERVICE_REFLECTOR)),
                    new AbsractnessCollector($container->get(WorseReflectionExtension::SERVICE_REFLECTOR)),
                ]
            );
        });


        $container->register('pacman.composer.local_repo', function (Container $container) {
            $composer = $container->get('pacman.composer.composer');
            assert($composer instanceof Composer);
            $manager = $composer->getRepositoryManager();
            return $manager->getLocalRepository();
        });

        $container->register('pacman.composer.composer', function (Container $container) {
            $factory = new Factory();
            $io = new NullIO();
            return $factory->createComposer($io);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema)
    {
    }
}
