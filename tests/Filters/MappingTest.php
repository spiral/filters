<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\SchemaBuilder;
use Spiral\Filters\Tests\UserDefined\InvalidFilter;
use Spiral\Models\Reflections\ReflectionEntity;

class MappingTest extends BaseTest
{
    /**
     * @expectedException \Spiral\Filters\Exceptions\MapperException
     */
    public function testInvalidPath()
    {
        $builder = new SchemaBuilder();
        $builder->register(new ReflectionEntity(InvalidFilter::class));

        $mapper = $this->getMapper();
        $mapper->setSchema($builder, false);

        $filter = new InvalidFilter(new ArrayInput([]), $mapper);
        $filter->isValid();
    }
}