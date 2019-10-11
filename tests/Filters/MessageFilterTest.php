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
use Spiral\Filters\Tests\Fixtures\MessageFilter;

class MessageFilterTest extends BaseTest
{
    public function testValid(): void
    {
        $filter = new MessageFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
    }

    public function testInvalid(): void
    {
        $filter = new MessageFilter(new ArrayInput([
            'key' => 'value'
        ]), $this->getMapper());

        $this->assertFalse($filter->isValid());

        $this->assertSame([
            'id' => 'ID is not valid.'
        ], $filter->getErrors());
    }
}
