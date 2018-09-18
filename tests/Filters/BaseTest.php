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
use Spiral\Filters\Bootloader\FiltersBootloader;
use Spiral\Filters\FilterMapper;
use Spiral\Filters\LocatorInterface;
use Spiral\Tokenizer\Bootloader\TokenizerBootloader;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Validation\Bootloader\ValidationBootloader;
use Spiral\Validation\Checker\AddressChecker;
use Spiral\Validation\Checker\FileChecker;
use Spiral\Validation\Checker\ImageChecker;
use Spiral\Validation\Checker\StringChecker;
use Spiral\Validation\Checker\TypeChecker;
use Spiral\Validation\Config\ValidatorConfig;
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