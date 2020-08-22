<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Tests\Filters\Fixtures;

use Spiral\Filters\Filter;

class ArrayIterateByPathFilter extends Filter
{
    public const SCHEMA = [
        'tests' => [TestFilter::class, 'custom.*', 'by']
    ];
}
