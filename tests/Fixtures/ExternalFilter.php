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

class ExternalFilter extends Filter
{
    public const SCHEMA = [
        'key' => 'key'
    ];

    public const VALIDATES = [
        'id' => ['notEmpty']
    ];
}
