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
use Spiral\Filters\Exceptions\MapperException;
use Spiral\Filters\Exceptions\SchemaException;
use Spiral\Filters\Schemas\SchemaBuilder;
use Spiral\Validation\ValidationInterface;
use Spiral\Validation\ValidatorInterface;

class InputMapper implements MapperInterface, SingletonInterface
{
    protected const MEMORY = 'filters';

    public const SH_RULES   = 0;
    public const SH_MAP     = 1;
    public const MAP_ORIGIN = 0;
    public const MAP_NESTED = 2;
    public const MAP_SOURCE = 3;
    public const MAP_ARRAY  = 6;

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
        foreach ($this->getSchema($filter)[self::SH_MAP] as $field => $map) {
            if (empty($map[self::MAP_NESTED])) {
                $filter->setField(
                    $field,
                    $input->getValue($map[self::MAP_SOURCE], $map[self::MAP_ORIGIN])
                );
                continue;
            }

            $nested = $map[self::MAP_NESTED];
            if (empty($map[self::MAP_ARRAY])) {
                // slicing down
                $filter->setField(
                    $field,
                    new $nested($input->withPrefix($map[self::MAP_ORIGIN]), $this)
                );
                continue;
            }

            foreach ($map[self::MAP_ORIGIN] as $index => $origin) {
                // slicing as array
                $filter->setField($field, new $nested($input->withPrefix($origin), $this));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function mapErrors(FilterInterface $filter, array $errors): array
    {
        $map = $this->getSchema($filter)[self::SH_MAP];

        //De-mapping
        $mapped = [];
        foreach ($errors as $field => $message) {
            if (!isset($map[$field])) {
                $mapped[$field] = $message;
                continue;
            }

            $this->mount($mapped, $map[$field][self::MAP_ORIGIN], $message);
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
            $this->getSchema($filter)[self::SH_RULES],
            $context
        );
    }

    /**
     * @inheritdoc
     */
    public function getSchema(FilterInterface $filter): array
    {
        if (empty($this->schema)) {
            $this->setSchema($this->buildSchema(), true);
        }

        if (!isset($this->schema[get_class($filter)])) {
            throw new SchemaException(sprintf(
                "Undefined filter `%s`, make sure schema to update schema.",
                get_class($filter)
            ));
        }

        return $this->schema[get_class($filter)];
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
     * @throws \Spiral\Filters\Exceptions\MapperException
     */
    private function mount(array &$array, string $path, $message)
    {
        if ($path == '.') {
            throw new MapperException(
                "Invalid input location with error `{$message}` (valid pattern - `data:field_name`)."
            );
        }

        $step = explode('.', $path);
        while ($name = array_shift($step)) {
            $array = &$array[$name];
        }

        $array = $message;
    }
}