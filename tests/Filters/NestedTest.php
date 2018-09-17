<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\ParentFilter;

class NestedTest extends BaseTest
{
    public function testChildrenValid()
    {
        $filter = new ParentFilter(new ArrayInput([
            'test' => [
                'id' => 'value'
            ]
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $this->assertSame('value', $filter->test->id);
    }

    public function testChildrenInvalid()
    {
        $filter = new ParentFilter(new ArrayInput([]), $this->getMapper());

        $this->assertFalse($filter->isValid());
        $this->assertSame([
            'test' => [
                'id' => 'This value is required.'
            ]
        ], $filter->getErrors());
    }
}