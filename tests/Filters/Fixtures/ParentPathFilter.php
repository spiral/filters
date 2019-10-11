<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Filters\Tests\Fixtures;

use Spiral\Filters\Filter;

class ParentPathFilter extends Filter
{
    public const SCHEMA = [
        'name' => 'name',
        'test' => [TestFilter::class, 'custom']
    ];
}
