<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\ParentDeepPathFilter;

class ParentDeepPathTest extends BaseTest
{
    public function testChildrenValid()
    {
        $filter = new ParentDeepPathFilter(new ArrayInput([
            'custom' => [
                'test' => [
                    'id' => 'value'
                ]
            ]
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $this->assertSame('value', $filter->test->id);
    }

    public function testChildrenInvalid()
    {
        $filter = new ParentDeepPathFilter(new ArrayInput([]), $this->getMapper());

        $this->assertFalse($filter->isValid());
        $this->assertSame([
            'custom' => [
                'test' => [
                    'id' => 'This value is required.'
                ]
            ]
        ], $filter->getErrors());
    }
}
