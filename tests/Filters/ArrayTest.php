<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\ArrayFilter;

class ArrayTest extends BaseTest
{
    public function testValid()
    {
        $filter = new ArrayFilter(new ArrayInput([
            'tests' => [
                ['id' => 'value'],
                ['id' => 'value2'],
            ]
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());

        $this->assertSame('value', $filter->tests[0]->id);
        $this->assertSame('value2', $filter->tests[1]->id);
    }

    public function testInvalid()
    {
        $filter = new ArrayFilter(new ArrayInput([
            'tests' => [
                0 => ['id' => 'value'],
                1 => ['id' => null],
            ]
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());

        $this->assertSame('value', $filter->tests[0]->id);
        $this->assertSame(null, $filter->tests[1]->id);

        $this->assertSame([
            'tests' => [
                1 => [
                    'id' => 'This value is required.'
                ]
            ]
        ], $filter->getErrors());
    }

    public function testEmptyValid()
    {
        $filter = new ArrayFilter(new ArrayInput([]), $this->getMapper());
        $this->assertTrue($filter->isValid());
    }
}