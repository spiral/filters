<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Filters;

use Spiral\Core\Container\SingletonInterface;
use Spiral\Filters\Exception\MapperException;
use Spiral\Filters\Exception\SchemaException;
use Spiral\Models\Reflection\ReflectionEntity;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidatorInterface;

final class FilterMapper implements MapperInterface, SingletonInterface
{
    // Packed schema definitions
    public const SOURCE         = 0;
    public const ORIGIN         = 1;
    public const FILTER         = 2;
    public const ARRAY          = 3;
    public const ITERATE_SOURCE = 4;
    public const ITERATE_ORIGIN = 5;

    /** @var CacheInterface */
    private $cache;

    /** @var ValidationInterface */
    private $validation;

    /**
     * @param ValidationInterface $validation
     * @param CacheInterface      $cache
     */
    public function __construct(ValidationInterface $validation, CacheInterface $cache = null)
    {
        $this->validation = $validation;
        $this->cache = $cache ?? new RuntimeCache();
    }

    /**
     * @inheritdoc
     */
    public function initValues(FilterInterface $filter, InputInterface $input)
    {
        foreach ($this->getSchema(get_class($filter))[Filter::SH_MAP] as $field => $map) {
            if (empty($map[self::FILTER])) {
                $filter->setField(
                    $field,
                    $input->getValue($map[self::SOURCE], $map[self::ORIGIN])
                );
                continue;
            }

            $nested = $map[self::FILTER];
            if (empty($map[self::ARRAY])) {
                // slicing down
                $filter->setField(
                    $field,
                    new $nested($input->withPrefix($map[self::ORIGIN]), $this)
                );
                continue;
            }

            $values = [];

            // List of "key" => "location in request"
            foreach ($this->iterate($input, $map) as $index => $origin) {
                $values[$index] = new $nested($input->withPrefix($origin), $this);
            }

            $filter->setField($field, $values);
        }
    }

    /**
     * @inheritdoc
     */
    public function mapErrors(FilterInterface $filter, array $errors): array
    {
        $map = $this->getSchema(get_class($filter))[Filter::SH_MAP];

        //De-mapping
        $mapped = [];
        foreach ($errors as $field => $message) {
            if (!isset($map[$field])) {
                $mapped[$field] = $message;
                continue;
            }

            $this->mount($mapped, $map[$field][self::ORIGIN], $message);
        }

        return $mapped;
    }

    /**
     * @inheritdoc
     */
    public function validate(FilterInterface $filter, $context = null): ValidatorInterface
    {
        return $this->validation->validate(
            $filter,
            $this->getSchema(get_class($filter))[Filter::SH_VALIDATES],
            $context
        );
    }

    /**
     * @inheritdoc
     */
    public function getSchema(string $filter): array
    {
        $schema = $this->cache->getSchema($filter);
        if ($schema === null) {
            $this->register($filter);
        }

        return $this->cache->getSchema($filter);
    }

    /**
     * @param string $filter
     *
     * @throws SchemaException
     */
    public function register(string $filter)
    {
        try {
            $builder = new SchemaBuilder(new ReflectionEntity($filter));
        } catch (\ReflectionException $e) {
            throw new SchemaException($e->getMessage(), $e->getCode(), $e);
        }

        $this->cache->setSchema($builder->getName(), $builder->makeSchema());
    }

    /**
     * Set element using dot notation.
     *
     * @param array  $array
     * @param string $path
     * @param mixed  $message
     *
     * @throws \Spiral\Filters\Exception\MapperException
     */
    private function mount(array &$array, string $path, $message)
    {
        if ($path == '.') {
            throw new MapperException(
                "Unable to mount error `{$message}` to `{$path}` (root path is forbidden)"
            );
        }

        $step = explode('.', $path);
        while ($name = array_shift($step)) {
            $array = &$array[$name];
        }

        $array = $message;
    }

    /**
     * Create set of origins and prefixed for a nested array of models.
     *
     * @param InputInterface $input
     * @param array          $map
     *
     * @return \Generator
     */
    private function iterate(InputInterface $input, array $map): \Generator
    {
        $values = $input->getValue($map[self::ITERATE_SOURCE], $map[self::ITERATE_ORIGIN]);
        if (empty($values) || !is_array($values)) {
            return [];
        }

        foreach (array_keys($values) as $key) {
            yield $key => $map[self::ORIGIN] . '.' . $key;
        }
    }
}