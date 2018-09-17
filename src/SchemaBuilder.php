<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Filters\Exceptions\SchemaException;
use Spiral\Models\Reflections\ReflectionEntity;

class SchemaBuilder
{
    // Used to define multiple nested models.
    protected const NESTED = 0;
    protected const ORIGIN = 1;
    protected const ITERATE = 2;

    /** @var ReflectionEntity[] */
    private $filters = [];

    /**
     * Register new filter entity.
     *
     * @param ReflectionEntity $entity
     */
    public function register(ReflectionEntity $entity)
    {
        $this->filters[] = $entity;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function has(string $class)
    {
        foreach ($this->filters as $filter) {
            if ($filter->getName() == $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate entity schema based on schema definitions.
     *
     * @return array
     *
     * @throws SchemaException
     */
    public function buildSchema(): array
    {
        $schema = [];
        foreach ($this->filters as $filter) {
            $schema[$filter->getName()] = [
                Filter::SH_MAP       => $this->buildMap($filter),
                Filter::SH_VALIDATES => $filter->getProperty('validates', true) ?? [],
                Filter::SH_SECURED   => $filter->getSecured(),
                Filter::SH_FILLABLE  => $filter->getFillable(),
                Filter::SH_MUTATORS  => $filter->getMutators(),
            ];
        }

        return $schema;
    }

    /**
     * @param ReflectionEntity $filter
     * @return array
     */
    protected function buildMap(ReflectionEntity $filter): array
    {
        $schema = $filter->getProperty('schema', true);
        if (empty($schema)) {
            throw new SchemaException("Filter `{$filter->getName()}` does not define any schema.");
        }

        $result = [];
        foreach ($schema as $field => $definition) {
            //Short definition
            if (is_string($definition)) {
                //Simple scalar field definition
                if (!class_exists($definition)) {
                    list($source, $origin) = $this->parseDefinition($filter, $field, $definition);
                    $result[$field] = [
                        FilterMapper::SOURCE => $source,
                        FilterMapper::ORIGIN => $origin
                    ];
                    continue;
                }

                if (!$this->has($definition)) {
                    throw new SchemaException(
                        "Invalid nested filter `{$definition}` at `{$filter->getName()}`->`{$field}`."
                    );
                }

                //Singular nested model
                $result[$field] = [
                    FilterMapper::SOURCE => null,
                    FilterMapper::ORIGIN => $field,
                    FilterMapper::FILTER => $definition,
                    FilterMapper::ARRAY  => false
                ];

                continue;
            }

            if (!is_array($definition) || count($definition) == 0) {
                throw new SchemaException("Invalid schema definition at `{$filter->getName()}`->`{$field}`.");
            }

            //Complex definition
            if (!empty($definition[self::ORIGIN])) {
                $origin = $definition[self::ORIGIN];

                // [class, 'data:something.*'] vs [class, 'data:something']
                $iterate = strpos($origin, '.*') !== false;
                $origin = rtrim($origin, '.*');
            } else {
                $origin = $field;
                $iterate = true;
            }

            //Array of models (default isolation prefix)
            $map = [
                FilterMapper::FILTER => $definition[self::NESTED],
                FilterMapper::SOURCE => null,
                FilterMapper::ORIGIN => $origin,
                FilterMapper::ARRAY  => $iterate
            ];

            if ($iterate) {
                list($source, $origin) = $this->parseDefinition(
                    $filter,
                    $field,
                    $definition[self::ITERATE] ?? $origin
                );

                $map[FilterMapper::ITERATE_SOURCE] = $source;
                $map[FilterMapper::ITERATE_ORIGIN] = $origin;
            }

            $result[$field] = $map;
        }

        return $result;
    }

    /**
     * Fetch source name and origin from schema definition. Support forms:
     *
     * field => source        => source:field
     * field => source:origin => source:origin
     *
     * @param ReflectionEntity $filter
     * @param string           $field
     * @param string           $definition
     *
     * @return array [$source, $origin]
     */
    private function parseDefinition(
        ReflectionEntity $filter,
        string $field,
        string $definition
    ): array {
        if (strpos($definition, ':') === false) {
            return [$filter->getProperty('default_source', true), $field, $definition];
        }

        return explode(':', $definition);
    }
}