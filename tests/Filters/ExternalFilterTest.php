<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\ExternalFilter;

// values not mentioned in schema
class ExternalFilterTest extends BaseTest
{
    public function testExternalValidation()
    {
        $filter = new ExternalFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());
        $filter->id = 'value';
        $this->assertTrue($filter->isValid());

        $this->assertSame('value', $filter->id);
    }

    public function testInvalid()
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
