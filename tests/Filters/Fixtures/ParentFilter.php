<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests\Fixtures;

use Spiral\Filters\Filter;

class ParentFilter extends Filter
{
    const SCHEMA = [
        'name' => 'name',
        'test' => TestFilter::class
    ];
}
