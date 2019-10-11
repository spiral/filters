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
     * @expectedException \Spiral\Filters\Exception\MapperException
     */
    public function testInvalidPath(): void
    {
        $mapper = $this->getMapper();
        $mapper->register(InvalidFilter::class);

        $filter = new InvalidFilter(new ArrayInput([]), $mapper);
        $filter->isValid();
    }
}
