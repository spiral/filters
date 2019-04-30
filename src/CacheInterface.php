<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Filters\Exception\SchemaException;

interface CacheInterface
{
    /**
     * Get compiled filter schema.
     *
     * @param string $filter
     * @return array|null
     *
     * @throws SchemaException
     */
    public function getSchema(string $filter): ?array;

    /**
     * Set compiled filter schema.
     *
     * @param string $filter
     * @param array  $schema
     *
     * @throws SchemaException
     */
    public function setSchema(string $filter, array $schema);
}