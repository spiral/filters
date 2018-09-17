<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\ArrayIterateByFilter;

class ArrayIterateByTest extends BaseTest
{
    public function testValid()
    {
        $filter = new ArrayIterateByFilter(new ArrayInput([
            'tests' => [
                ['id' => 'value'],
                ['id' => 'value2'],
            ],
            'by'    => [1, 2]
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());

        $this->assertSame('value', $filter->tests[0]->id);
        $this->assertSame('value2', $filter->tests[1]->id);
    }

    public function testInvalid()
    {
        $filter = new ArrayIterateByFilter(new ArrayInput([
            'tests' => [
                'a' => ['id' => 'value'],
                'b' => ['id' => null],
            ],
            'by'    => ['a' => 1, 'b' => 2, 'c' => 3]
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());

        $this->assertSame('value', $filter->tests['a']->id);
        $this->assertSame(null, $filter->tests['b']->id);
        $this->assertSame(null, $filter->tests['c']->id);

        $this->assertSame([
            'tests' => [
                'b' => [
                    'id' => 'This value is required.'
                ],
                'c' => [
                    'id' => 'This value is required.'
                ],
            ]
        ], $filter->getErrors());
    }

    public function testEmptyValid()
    {
        $filter = new ArrayIterateByFilter(new ArrayInput([]), $this->getMapper());
        $this->assertTrue($filter->isValid());
    }
}