<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Core\NullMemory;
use Spiral\Filters\FilterLocator;
use Spiral\Filters\FilterMapper;
use Spiral\Filters\LocatorInterface;
use Spiral\Filters\MapperInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Validation\Checker\AddressChecker;
use Spiral\Validation\Checker\FileChecker;
use Spiral\Validation\Checker\ImageChecker;
use Spiral\Validation\Checker\StringChecker;
use Spiral\Validation\Checker\TypeChecker;
use Spiral\Validation\Config\ValidatorConfig;
use Spiral\Validation\ParserInterface;
use Spiral\Validation\RuleParser;
use Spiral\Validation\RulesInterface;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidationProvider;

abstract class BaseTest extends TestCase
{
    protected $container;

    const TOKENIZER_CONFIG = [
        'directories' => [__DIR__ . '/Fixtures/'],
        'exclude'     => ['User'],
    ];

    const VALIDATION_CONFIG = [
        'checkers' => [
            'file'    => FileChecker::class,
            'image'   => ImageChecker::class,
            'type'    => TypeChecker::class,
            'address' => AddressChecker::class,
            'string'  => StringChecker::class
        ],
        'aliases'  => [
            'notEmpty' => 'type::notEmpty',
            'email'    => 'address::email',
            'url'      => 'address::url',
        ],
    ];

    public function setUp()
    {
        $this->container = new Container();

        $this->container->bindSingleton(ClassesInterface::class, ClassLocator::class);

        $this->container->bindSingleton(ValidationInterface::class, ValidationProvider::class);
        $this->container->bindSingleton(RulesInterface::class, ValidationProvider::class);
        $this->container->bindSingleton(ParserInterface::class, RuleParser::class);

        $this->container->bindSingleton(MapperInterface::class, FilterMapper::class);
        $this->container->bindSingleton(LocatorInterface::class, FilterLocator::class);


        $this->container->bind(
            TokenizerConfig::class,
            new TokenizerConfig(static::TOKENIZER_CONFIG)
        );

        $this->container->bind(
            ValidatorConfig::class,
            new ValidatorConfig(static::VALIDATION_CONFIG)
        );
    }

    protected function getMapper(): FilterMapper
    {
        return new FilterMapper(
            new NullMemory(),
            $this->container->get(LocatorInterface::class),
            $this->container->get(ValidationInterface::class)
        );
    }
}