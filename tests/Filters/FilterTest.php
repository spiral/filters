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
use Spiral\Filters\Tests\Fixtures\TestFilter;

class FilterTest extends BaseTest
{
    public function testValid(): void
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $this->assertSame('value', $filter->id);
    }

    public function testInvalid(): void
    {
        $filter = new TestFilter(new ArrayInput([
            'key' => 'value'
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());

        $this->assertSame([
            'id' => 'This value is required.'
        ], $filter->getErrors());
    }

    public function testContext(): void
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertSame(null, $filter->getContext());
        $this->assertTrue($filter->isValid());

        $filter->setContext('value');
        $this->assertSame('value', $filter->getContext());
        $this->assertTrue($filter->isValid());

        $filter->setContext(null);
        $this->assertSame(null, $filter->getContext());
        $this->assertTrue($filter->isValid());
    }

    public function testSetRevalidate(): void
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        $filter->id = null;
        $this->assertFalse($filter->isValid());
    }

    public function testUnsetRevalidate(): void
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
        unset($filter->id);
        $this->assertFalse($filter->isValid());
    }

    public function testDebugInfo(): void
    {
        $filter = new TestFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $info = $filter->__debugInfo();

        $this->assertSame([
            'valid'  => true,
            'fields' => [
                'id' => 'value',
            ],
            'errors' => []
        ], $info);
    }
}
