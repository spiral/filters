<?php

declare(strict_types=1);

namespace Spiral\Tests\Filters\Model\Schema;

use Mockery as m;
use Spiral\Attributes\AttributeReader;
use Spiral\Filters\Attribute\Input\Attribute;
use Spiral\Filters\Attribute\Input\BearerToken;
use Spiral\Filters\Attribute\Input\Cookie;
use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Input\Query;
use Spiral\Filters\Attribute\NestedArray;
use Spiral\Filters\Attribute\NestedFilter;
use Spiral\Filters\Model\FilterInterface;
use Spiral\Filters\Model\FilterProviderInterface;
use Spiral\Filters\Model\Schema\AttributeMapper;
use Spiral\Filters\Exception\ValidationException;
use Spiral\Filters\InputInterface;
use Spiral\Tests\Filters\BaseTest;

final class AttributeMapperTest extends BaseTest
{
    private m\LegacyMockInterface|m\MockInterface|FilterProviderInterface $provider;
    private AttributeMapper $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = new AttributeMapper(
            $this->provider = m::mock(FilterProviderInterface::class),
            new AttributeReader()
        );
    }

    public function testInputAttribute(): void
    {
        $input = m::mock(InputInterface::class);
        $barInput = m::mock(InputInterface::class);
        $bazInputFirst = m::mock(InputInterface::class);
        $bazInputSecond = m::mock(InputInterface::class);

        $input->shouldReceive('withPrefix')->once()->with('bar')->andReturn($barInput);
        $input->shouldReceive('withPrefix')->once()->with('bazFilter.first')->andReturn($bazInputFirst);
        $input->shouldReceive('withPrefix')->once()->with('bazFilter.second')->andReturn($bazInputSecond);
        $input->shouldReceive('withPrefix')->once()->with('fooFilter')->andReturn($input);

        $this->provider->shouldReceive('createFilter')->once()->with('fooFilter', $input)
            ->andReturn($fooFilter = m::mock(FilterInterface::class));
        $this->provider->shouldReceive('createFilter')->once()->with('barFilter', $barInput)
            ->andReturn($barFilter = m::mock(FilterInterface::class));

        $this->provider->shouldReceive('createFilter')->once()->with('bazFilter', $bazInputFirst)
            ->andReturn($bazFilterFirst = m::mock(FilterInterface::class));
        $this->provider->shouldReceive('createFilter')->once()->with('bazFilter', $bazInputSecond)
            ->andReturn($bazFilterSecond = m::mock(FilterInterface::class));

        $input->shouldReceive('getValue')->once()->with('post', 'username')->andReturn('john_smith');
        $input->shouldReceive('getValue')->once()->with('post', 'first_name')->andReturn('john');
        $input->shouldReceive('getValue')->once()->with('query', 'lastName')->andReturn('smith');
        $input->shouldReceive('getValue')->once()->with('query', 'page_num')->andReturn(10);
        $input->shouldReceive('getValue')->once()->with('attribute', 'wsPath')->andReturn('/foo/bar');
        $input->shouldReceive('getValue')->once()->with('bearerToken')->andReturn('bearer:token');
        $input->shouldReceive('getValue')->once()->with('cookie', 'utm_source')->andReturn('google');
        $input->shouldReceive('getValue')->once()->with('cookie', 'utmId')->andReturn('abc.123');
        $input->shouldReceive('getValue')->once()->with('post', 'bazFilter')
            ->andReturn(['first' => ['foo' => 'bar'], 'second' => ['foo' => 'baz']]);

        [$schema, $errors] = $this->mapper->map(
            $filter = new class implements FilterInterface {
                #[Post]
                public string $username;

                #[Post(key: 'first_name')]
                public string $name;

                #[Query]
                public string $lastName;

                #[Query(key: 'page_num')]
                public int $page;

                #[Attribute]
                private string $wsPath;

                #[BearerToken]
                public string $token;

                #[Cookie(key: 'utm_source')]
                public string $utmSource;

                #[Cookie]
                public string $utmId;

                #[NestedFilter(class: 'fooFilter')]
                public FilterInterface $fooFilter;

                #[NestedFilter(class: 'barFilter', prefix: 'bar')]
                public FilterInterface $barFilter;

                #[NestedArray(class: 'bazFilter', input: new Post())]
                public array $bazFilter;

                public function getWsPath(): string
                {
                    return $this->wsPath;
                }
            },
            $input
        );

        $this->assertSame('john_smith', $filter->username);
        $this->assertSame('john', $filter->name);
        $this->assertSame('smith', $filter->lastName);
        $this->assertSame(10, $filter->page);
        $this->assertSame('bearer:token', $filter->token);
        $this->assertSame('/foo/bar', $filter->getWsPath());
        $this->assertSame('google', $filter->utmSource);
        $this->assertSame('abc.123', $filter->utmId);
        $this->assertSame($fooFilter, $filter->fooFilter);
        $this->assertSame($barFilter, $filter->barFilter);
        $this->assertSame([
            'first' => $bazFilterFirst,
            'second' => $bazFilterSecond,
        ], $filter->bazFilter);

        $this->assertSame([
            'username' => 'post:username',
            'name' => 'post:first_name',
            'lastName' => 'query:lastName',
            'page' => 'query:page_num',
            'wsPath' => 'attribute:wsPath',
            'token' => 'bearerToken',
            'utmSource' => 'cookie:utm_source',
            'utmId' => 'cookie:utmId',
            'fooFilter' => 'fooFilter',
            'barFilter' => ['barFilter', 'bar'],
            'bazFilter' => ['bazFilter', 'bazFilter.*'],
        ], $schema);

        $this->assertSame([], $errors);
    }

    public function testNestedFiltersValidationError(): void
    {
        $input = m::mock(InputInterface::class);
        $bazInputFirst = m::mock(InputInterface::class);
        $bazInputSecond = m::mock(InputInterface::class);
        $input->shouldReceive('withPrefix')->once()->with('baz.first')->andReturn($bazInputFirst);
        $input->shouldReceive('withPrefix')->once()->with('baz.second')->andReturn($bazInputSecond);
        $input->shouldReceive('withPrefix')->once()->with('fooFilter')->andReturn($input);

        $this->provider->shouldReceive('createFilter')->once()->with('fooFilter', $input)
            ->andThrow(new ValidationException(['fooFilter' => 'Error']));

        $this->provider->shouldReceive('createFilter')->once()->with('bazFilter', $bazInputFirst)
            ->andReturn($bazFilterFirst = m::mock(FilterInterface::class));
        $this->provider->shouldReceive('createFilter')->once()->with('bazFilter', $bazInputSecond)
            ->andThrow(new ValidationException(['field' => 'Error']));

        $input->shouldReceive('getValue')->once()->with('post', 'username')->andReturn('john_smith');
        $input->shouldReceive('getValue')->once()->with('post', 'bazFilter')
            ->andReturn(['first' => ['foo' => 'bar'], 'second' => ['foo' => 'baz']]);

        [$schema, $errors] = $this->mapper->map(
            $filter = new class implements FilterInterface {
                #[Post]
                public string $username;

                #[NestedFilter(class: 'fooFilter')]
                public FilterInterface $fooFilter;

                #[NestedArray(class: 'bazFilter', input: new Post(), prefix: 'baz')]
                public array $bazFilter;
            },
            $input
        );

        $this->assertSame([
            'username' => 'post:username',
            'fooFilter' => 'fooFilter',
            'bazFilter' => ['bazFilter', 'baz.*'],
        ], $schema);

        $this->assertSame([
            'fooFilter' => [
                'fooFilter' => 'Error'
            ],
            'bazFilter' => [
                'second' => [
                    'field' => 'Error',
                ],
            ],
        ], $errors);
    }
}
