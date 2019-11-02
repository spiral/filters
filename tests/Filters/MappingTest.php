<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\UserDefined\InvalidFilter;

class MappingTest extends BaseTest
{
    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     */
    public function testInvalidPath(): void
    {
        $filter = $this->getProvider()->createFilter(InvalidFilter::class, new ArrayInput([]));
        dump($filter);
    }
}
