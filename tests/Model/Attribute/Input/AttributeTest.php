<?php

declare(strict_types=1);

namespace Spiral\Tests\Filters\Model\Attribute\Input;

use Spiral\Filters\Attribute\Input\Attribute;

final class AttributeTest extends \Spiral\Tests\Filters\Model\AttributeTestCase
{
    public function testGetsValueForDefinedKey(): void
    {
        $attribute = new Attribute('foo');

        $this->input
            ->shouldReceive('getValue')
            ->once()
            ->with('attribute', 'foo')
            ->andReturn('bar');

        self::assertSame('bar', $attribute->getValue($this->input, $this->makeProperty()));
    }

    public function testGetsSchemaForDefinedKey(): void
    {
        $attribute = new Attribute('foo');

        self::assertSame('attribute:foo', $attribute->getSchema($this->makeProperty()));
    }

    public function testGetsValueForNotDefinedKey(): void
    {
        $attribute = new Attribute();

        $this->input
            ->shouldReceive('getValue')
            ->once()
            ->with('attribute', 'baz')
            ->andReturn('bar');

        self::assertSame('bar', $attribute->getValue($this->input, $this->makeProperty()));
    }

    public function testGetsSchemaForNotDefinedKey(): void
    {
        $attribute = new Attribute();

        self::assertSame('attribute:baz', $attribute->getSchema($this->makeProperty()));
    }
}
