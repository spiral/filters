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
                Filter::SH_MAP      => $this->buildMap($filter),
                Filter::SH_SECURED  => $filter->getProperty('validates', true),
                Filter::SH_SECURED  => $filter->getSecured(),
                Filter::SH_FILLABLE => $filter->getFillable(),
                Filter::SH_MUTATORS => $filter->getMutators(),
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
            throw new SchemaException("Filter `{$filter}` does not define any schema.");
        }

        // todo: process schema


        return null;
    }
}