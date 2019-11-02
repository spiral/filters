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
use Spiral\Filters\SchemaBuilder;
use Spiral\Filters\Tests\UserDefined\BrokenFilter;
use Spiral\Filters\Tests\UserDefined\EmptyFilter;
use Spiral\Models\Reflection\ReflectionEntity;

class SchemasTest extends BaseTest
{
    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     */
    public function testUndefinedSchema(): void
    {
        $this->getProvider()->createFilter('undefined', new ArrayInput());
    }

    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     */
    public function testEmptySchema(): void
    {
        $builder = new SchemaBuilder(new ReflectionEntity(EmptyFilter::class));
        $builder->makeSchema();
    }

    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     * @expectedExceptionMessageRegExp /id/
     */
    public function testBrokenFilter(): void
    {
        $builder = new SchemaBuilder(new ReflectionEntity(BrokenFilter::class));
        $builder->makeSchema();
    }
}
