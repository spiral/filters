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
use Spiral\Filters\Tests\Fixtures\ParentDeepPathFilter;

class ParentDeepPathTest extends BaseTest
{
    public function testChildrenValid(): void
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

    public function testChildrenInvalid(): void
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
