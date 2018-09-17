<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\TestFilter;

class FilterTest extends BaseTest
{
    public function testValid()
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
    }

    public function testInvalid()
    {
        $filter = new TestFilter(new ArrayInput([
            'key' => 'value'
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());

        $this->assertSame([
            'id' => 'This value is required.'
        ], $filter->getErrors());
    }
}