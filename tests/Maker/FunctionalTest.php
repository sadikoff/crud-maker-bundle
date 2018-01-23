<?php

namespace Koff\Bundle\CrudMakerBundle\Tests\Maker;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Koff\Bundle\CrudMakerBundle\CrudMakerBundle;
use Koff\Bundle\CrudMakerBundle\Maker\MakeCrud;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MakerBundle\Command\MakerCommand;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Routing\RouterInterface;

class FunctionalTest extends TestCase
{
    private $fs;
    private $targetDir;

    public function setUp()
    {
        $this->targetDir = sys_get_temp_dir().'/'.uniqid('sf_maker_', true);
        $this->fs = new Filesystem();
        $this->fs->mkdir($this->targetDir);
    }

    public function tearDown()
    {
        //$this->fs->remove($this->targetDir);
    }

    /**
     * @dataProvider getCommandTests
     */
    public function testCommands(MakerInterface $maker, array $inputs)
    {
        $command = new MakerCommand($maker, $this->createGenerator());
        $command->setCheckDependencies(false);

        $tester = new CommandTester($command);
        $tester->setInputs($inputs);
        $tester->execute(array());

        $this->assertContains('Success', $tester->getDisplay());

        $files = $this->parsePHPFiles($tester->getDisplay());
        foreach ($files as $file) {
            $process = new Process(sprintf('php vendor/bin/php-cs-fixer fix --dry-run --diff %s', $this->targetDir.'/'.$file), __DIR__.'/../../');
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \Exception(sprintf('File "%s" has a php-cs problem: %s', $file, $process->getOutput()));
            }
        }
    }

    public function getCommandTests()
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())
               ->method('getRouteCollection')
               ->willReturn(new RouteCollection());

        $metadata = $this->createMock(ClassMetadataInfo::class);
        $metadata->identifier = ['id'];
        $metadata->fieldMappings = [['fieldName' => 'id'], ['fieldName' => 'title']];

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
                      ->method('getClassMetadata')
                      ->withAnyParameters()
                      ->willReturn($metadata);

        yield 'crud' => array(
            new MakeCrud($router, $entityManager),
            array(
                // entity
                'FooBar',
            ),
        );
    }

//    /**
//     * Smoke test to make sure the DI autowiring works and all makers
//     * are registered and have the correct arguments.
//     */
//    public function testWiring()
//    {
//        $entityManager = $this->createMock(EntityManagerInterface::class);
//
//        $kernel = new FunctionalTestKernel('dev', true);
//
//        $finder = new Finder();
//        $finder->in(__DIR__.'/../../src/Maker');
//
//        $application = new Application($kernel);
//
//        var_dump($application);die;
//
//        foreach ($finder as $file) {
//            $class = 'Koff\Bundle\CrudMakerBundle\Maker\\'.$file->getBasename('.php');
//
//            $commandName = $class::getCommandName();
//            // if the command does not exist, this will explode
//            $command = $application->find($commandName);
//            // just a smoke test assert
//            $this->assertInstanceOf(MakerCommand::class, $command);
//        }
//    }

    private function createGenerator()
    {
        return new Generator(new FileManager(new Filesystem(), $this->targetDir));
    }

    private function parsePHPFiles($output)
    {
        $files = array();
        foreach (explode("\n", $output) as $line) {
            if (false === strpos($line, 'created:')) {
                continue;
            }

            list(, $filename) = explode(':', $line);
            $files[] = trim($filename);
        }

        return $files;
    }
}

class FunctionalTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new MakerBundle(),
            new CrudMakerBundle(),
        );
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->setParameter('kernel.secret', 123);
    }

    public function getRootDir()
    {
        return sys_get_temp_dir().'/'.uniqid('sf_maker_', true);
    }
}
