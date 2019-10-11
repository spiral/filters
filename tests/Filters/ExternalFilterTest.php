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
use Spiral\Filters\Tests\Fixtures\ExternalFilter;

// values not mentioned in schema
class ExternalFilterTest extends BaseTest
{
    public function testExternalValidation(): void
    {
        $filter = new ExternalFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());
        $filter->id = 'value';
        $this->assertTrue($filter->isValid());

        $this->assertSame('value', $filter->id);
    }

    public function testInvalid(): void
    {
        $filter = new ExternalFilter(new ArrayInput([
            'key' => 'value'
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());

        $this->assertSame([
            'id' => 'This value is required.'
        ], $filter->getErrors());
    }
}
