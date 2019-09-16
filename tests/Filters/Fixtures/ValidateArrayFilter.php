<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests\Fixtures;

use Spiral\Filters\Filter;

class ValidateArrayFilter extends Filter
{
    const SCHEMA = [
        'tests' => [TestFilter::class]
    ];

    const VALIDATES = [
        'tests' => [
            ['notEmpty', 'err' => '[[No tests are specified.]]']
        ]
    ];
}
