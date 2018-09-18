<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\MemoryInterface;
use Spiral\Filters\Exception\MapperException;
use Spiral\Filters\Exception\SchemaException;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidatorInterface;

final class FilterMapper implements MapperInterface, SingletonInterface
{
    protected const MEMORY = 'filters';

    // Packed schema definitions
    public const SOURCE = 0;
    public const ORIGIN = 1;
    public const FILTER = 2;
    public const ARRAY = 3;
    public const ITERATE_SOURCE = 4;
    public const ITERATE_ORIGIN = 5;

    /** @var MemoryInterface */
    private $memory;

    /** @var LocatorInterface */
    private $locator;

    /** @var ValidationInterface */
    private $validation;

    /** @var array */
    private $schema;

    /**
     * @param MemoryInterface     $memory
     * @param LocatorInterface    $locator
     * @param ValidationInterface $validation
     */
    public function __construct(
        MemoryInterface $memory,
        LocatorInterface $locator,
        ValidationInterface $validation
    ) {
        $this->memory = $memory;
        $this->locator = $locator;
        $this->validation = $validation;

        $this->schema = $this->loadSchema();
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
        if (empty($this->schema)) {
            $this->setSchema($this->buildSchema($this->locator), true);
        }

        if (!isset($this->schema[$filter])) {
            throw new SchemaException(
                "Undefined filter `{$filter}`, make sure schema to update schema."
            );
        }

        return $this->schema[$filter];
    }

    /**
     * Generate filters schema.
     *
     * @param LocatorInterface|null $locator
     * @return SchemaBuilder
     *
     * @throws SchemaException
     */
    public function buildSchema(LocatorInterface $locator = null): SchemaBuilder
    {
        $builder = new SchemaBuilder();
        if (!empty($locator)) {
            foreach ($locator->locateFilters() as $filter) {
                $builder->register($filter);
            }
        }

        return $builder;
    }

    /**
     * Update filter schema using schema builder.
     *
     * @param SchemaBuilder $builder
     * @param bool          $memorize
     */
    public function setSchema(SchemaBuilder $builder, bool $memorize = false)
    {
        $this->schema = $builder->buildSchema();

        if ($memorize) {
            $this->memory->saveData(static::MEMORY, $this->schema);
        }
    }

    /**
     * Reset filters schema.
     */
    public function resetSchema()
    {
        $this->schema = [];
        $this->memory->saveData(self::MEMORY, []);
    }

    /**
     * Load packed schema from memory.
     *
     * @return array
     */
    private function loadSchema(): array
    {
        return (array)$this->memory->loadData(static::MEMORY);
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
                "Unable to mount error `{$message}` to `{$path}` (root path is forbidden)."
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