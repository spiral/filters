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
        $this->assertFalse($filter->hasErrors());
        $this->assertFalse($filter->hasErrors('id'));
        $this->assertFalse($filter->hasErrors('key'));
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

        $this->assertTrue($filter->hasErrors());
        $this->assertTrue($filter->hasErrors('id'));
        $this->assertFalse($filter->hasErrors('key'));
    }

    public function testReset()
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $filter->reset();
        $this->assertTrue($filter->isValid());
    }

    public function testSetRevalidate()
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $filter->id = null;
        $this->assertFalse($filter->isValid());
    }

    public function testUnsetRevalidate()
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        unset($filter->id);
        $this->assertFalse($filter->isValid());
    }

    public function testDebugInfo()
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $info = $filter->__debugInfo();

        $this->assertSame([
            'valid'  => true,
            'fields' => [
                'id'  => 'value',
                'key' => null
            ],
            'errors' => []
        ], $info);
    }
}