<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\ParentPathFilter;

class ParentPathTest extends BaseTest
{
    public function testChildrenValid()
    {
        $filter = new ParentPathFilter(new ArrayInput([
            'custom' => [
                'id' => 'value'
            ]
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $this->assertSame('value', $filter->test->id);
    }

    public function testChildrenInvalid()
    {
        $filter = new ParentPathFilter(new ArrayInput([]), $this->getMapper());

        $this->assertFalse($filter->isValid());
        $this->assertSame([
            'custom' => [
                'id' => 'This value is required.'
            ]
        ], $filter->getErrors());
    }
}
