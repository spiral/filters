<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Bootloaders;

use Spiral\Filters\FilterLocator;
use Spiral\Filters\InputMapper;
use Spiral\Filters\LocatorInterface;
use Spiral\Filters\MapperInterface;

class FiltersBootloader extends Bootloader
{
    const SINGLETONS = [
        MapperInterface::class  => InputMapper::class,
        LocatorInterface::class => FilterLocator::class
    ];
}