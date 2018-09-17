<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\ArrayInput;
use Spiral\Filters\Tests\Fixtures\MessageFilter;

class MessageFilterTest extends BaseTest
{
    public function testValid()
    {
        $filter = new MessageFilter(new ArrayInput([
            'id' => 'value'
        ]), $this->getMapper());

        $this->assertTrue($filter->isValid());
    }

    public function testInvalid()
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