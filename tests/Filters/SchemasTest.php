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
use Spiral\Models\Reflections\ReflectionEntity;

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

        $mapper->resetSchema();
        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);
    }

    /**
     * @expectedException \Spiral\Filters\Exceptions\SchemaException
     */
    public function testUndefinedSchema()
    {
        $this->getMapper()->getSchema('undefined');
    }

    /**
     * @expectedException \Spiral\Filters\Exceptions\SchemaException
     */
    public function testMissingRelation()
    {
        $builder = new SchemaBuilder();
        $builder->register(new ReflectionEntity(ParentFilter::class));
        $builder->buildSchema();
    }

    public function testCustomBuilder()
    {
        $mapper = $this->getMapper();
        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);

        $builder = new SchemaBuilder();
        $builder->register(new ReflectionEntity(ParentFilter::class));
        $builder->register(new ReflectionEntity(TestFilter::class));

        $mapper->setSchema($builder, false);
        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);
    }

    /**
     * @expectedException \Spiral\Filters\Exceptions\SchemaException
     */
    public function testCustomButMissing()
    {
        $mapper = $this->getMapper();
        $schema = $mapper->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);

        $builder = new SchemaBuilder();
        $builder->register(new ReflectionEntity(ParentFilter::class));
        $builder->register(new ReflectionEntity(TestFilter::class));

        $mapper->setSchema($builder, false);
        $schema = $mapper->getSchema(ExternalFilter::class);
        $this->assertNotEmpty($schema);
    }
}