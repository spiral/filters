<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Core\NullMemory;
use Spiral\Filters\Bootloaders\FiltersBootloader;
use Spiral\Filters\FilterMapper;
use Spiral\Filters\LocatorInterface;
use Spiral\Filters\MapperInterface;
use Spiral\Tokenizer\Bootloaders\TokenizerBootloader;
use Spiral\Tokenizer\Configs\TokenizerConfig;
use Spiral\Validation\Bootloaders\ValidationBootloader;
use Spiral\Validation\Checkers\AddressChecker;
use Spiral\Validation\Checkers\FileChecker;
use Spiral\Validation\Checkers\ImageChecker;
use Spiral\Validation\Checkers\StringChecker;
use Spiral\Validation\Checkers\TypeChecker;
use Spiral\Validation\Configs\ValidatorConfig;
use Spiral\Validation\ValidationInterface;

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
        $bootloder = new BootloadManager($this->container);
        $bootloder->bootload([
            TokenizerBootloader::class,
            ValidationBootloader::class,
            FiltersBootloader::class
        ]);

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