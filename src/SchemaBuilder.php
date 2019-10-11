<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Filters;

use Spiral\Filters\Exception\SchemaException;
use Spiral\Models\Reflection\ReflectionEntity;

final class SchemaBuilder
{
    // Used to define multiple nested models.
    protected const NESTED  = 0;
    protected const ORIGIN  = 1;
    protected const ITERATE = 2;

    /** @var ReflectionEntity */
    private $entity;

    /**
     * @param ReflectionEntity $entity
     */
    public function __construct(ReflectionEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->entity->getName();
    }

    /**
     * Generate entity schema based on schema definitions.
     *
     * @return array
     *
     * @throws SchemaException
     */
    public function makeSchema(): array
    {
        return [
            Filter::SH_MAP       => $this->buildMap($this->entity),
            Filter::SH_VALIDATES => $this->entity->getProperty('validates', true) ?? [],
            Filter::SH_SECURED   => $this->entity->getSecured(),
            Filter::SH_FILLABLE  => $this->entity->getFillable(),
            Filter::SH_MUTATORS  => $this->entity->getMutators(),
        ];
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
                $iterate = strpos($origin, '.*') !== false || !empty($definition[self::ITERATE]);
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
            return [$filter->getProperty('default_source', true), $definition ?? $field];
        }

        return explode(':', $definition);
    }
}
