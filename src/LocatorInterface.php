<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Models\Reflections\ReflectionEntity;

interface LocatorInterface
{
    /**
     * @return \Generator|ReflectionEntity[]
     */
    public function locateFilters(): \Generator;
}