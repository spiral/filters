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
use Spiral\Filters\Exception\SchemaException;
use Spiral\Filters\SchemaBuilder;
use Spiral\Filters\Tests\UserDefined\BrokenFilter;
use Spiral\Filters\Tests\UserDefined\EmptyFilter;
use Spiral\Models\Reflection\ReflectionEntity;

class SchemasTest extends BaseTest
{
    public function testUndefinedSchema(): void
    {
        $this->expectException(SchemaException::class);
        $this->getProvider()->createFilter('undefined', new ArrayInput());
    }

    public function testEmptySchema(): void
    {
        $this->expectException(SchemaException::class);
        $builder = new SchemaBuilder(new ReflectionEntity(EmptyFilter::class));
        $builder->makeSchema();
    }

    public function testBrokenFilter(): void
    {
        $this->expectExceptionMessageMatches('/id/');
        $this->expectException(SchemaException::class);
        $builder = new SchemaBuilder(new ReflectionEntity(BrokenFilter::class));
        $builder->makeSchema();
    }
}
