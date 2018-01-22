<?php

namespace Koff\Bundle\CrudMakerBundle\Maker;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeCrud implements MakerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getCommandName(): string
    {
        return 'make:crud';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates crud for Doctrine entity class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create crud (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeCrud.txt'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Route::class,
            'annotations'
        );

        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(InputInterface $input): array
    {
        $entityClassName = Str::asClassName($input->getArgument('entity-class'));
        Validator::validateClassName($entityClassName);
        $controllerClassName = Str::asClassName($entityClassName, 'Controller');
        Validator::validateClassName($controllerClassName);
        $formClassName = Str::asClassName($entityClassName, 'Type');
        Validator::validateClassName($formClassName);

        return [
            'controller_class_name' => $controllerClassName,
            'entity_class_name' => $entityClassName,
            'form_class_name' => $formClassName,
            'route_path' => Str::asRoutePath(str_replace('Controller', '', $controllerClassName)),
            'route_name' => Str::asRouteName(str_replace('Controller', '', $controllerClassName)),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(array $params): array
    {
        return [
            __DIR__.'/../Resources/skeleton/controller/Controller.tpl.php' => 'src/Controller/'.$params['controller_class_name'].'.php',
            __DIR__.'/../Resources/skeleton/form/Type.tpl.php' => 'src/Form/'.$params['form_class_name'].'.php',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function writeNextStepsMessage(array $params, ConsoleStyle $io)
    {
        if (!count($this->router->getRouteCollection())) {
            $io->text('<error> Warning! </> No routes configuration defined yet.');
            $io->text('           You should probably uncomment the annotation routes in <comment>config/routes.yaml</>');
            $io->newLine();
        }
        $io->text('Next: Check your new crud class!');
    }
}
