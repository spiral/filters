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
use Spiral\Filters\Tests\Fixtures\ParentFilter;

class NestedTest extends BaseTest
{
    public function testChildrenValid(): void
    {
        $filter = $this->getProvider()->createFilter(ParentFilter::class, new ArrayInput([
            'test' => [
                'id' => 'value'
            ]
        ]));

        $this->assertTrue($filter->isValid());
        $this->assertSame('value', $filter->test->id);
    }

    public function testChildrenInvalid(): void
    {
        $filter = $this->getProvider()->createFilter(ParentFilter::class, new ArrayInput([]));

        $this->assertFalse($filter->isValid());
        $this->assertSame([
            'test' => [
                'id' => 'This value is required.'
            ]
        ], $filter->getErrors());
    }
}
