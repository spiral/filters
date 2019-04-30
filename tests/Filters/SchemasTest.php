<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\SchemaBuilder;
use Spiral\Filters\Tests\Fixtures\ExternalFilter;
use Spiral\Filters\Tests\Fixtures\ParentFilter;
use Spiral\Filters\Tests\Fixtures\TestFilter;
use Spiral\Filters\Tests\UserDefined\BrokenFilter;
use Spiral\Filters\Tests\UserDefined\EmptyFilter;
use Spiral\Models\Reflection\ReflectionEntity;

class SchemasTest extends BaseTest
{
    public function testSchemas()
    {
        $schema = $this->getMapper()->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);
    }

    public function testSchemasAfterReset()
    {
        $mapper = $this->getMapper();
        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);
    }

    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     */
    public function testUndefinedSchema()
    {
        $this->getMapper()->getSchema('undefined');
    }

    public function testCustomBuilder()
    {
        $mapper = $this->getMapper();
        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);

        $mapper->register(ParentFilter::class);
        $mapper->register(TestFilter::class);

        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);
    }

    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     */
    public function testEmptySchema()
    {
        $builder = new SchemaBuilder(new ReflectionEntity(EmptyFilter::class));
        $builder->makeSchema();
    }

    /**
     * @expectedException \Spiral\Filters\Exception\SchemaException
     * @expectedExceptionMessageRegExp /id/
     */
    public function testBrokenFilter()
    {
        $builder = new SchemaBuilder(new ReflectionEntity(BrokenFilter::class));
        $builder->makeSchema();
    }
}