<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

final class RuntimeCache implements CacheInterface
{
    /** @var array */
    private $schema = [];

    /**
     * @inheritdoc
     */
    public function getSchema(string $filter): ?array
    {
        return $this->schema[$filter] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setSchema(string $filter, array $schema)
    {
        $this->schema[$filter] = $schema;
    }
}
