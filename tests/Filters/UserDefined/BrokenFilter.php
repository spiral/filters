<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests\UserDefined;

use Spiral\Filters\Filter;

class BrokenFilter extends Filter
{
    public const SCHEMA = [
        'id' => []
    ];
}