<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests\Fixtures;

use Spiral\Filters\Filter;

abstract class AbstractFilter extends Filter
{
    const SCHEMA = [
        'id' => 'query:id'
    ];

    const VALIDATES = [
        'id' => ['notEmpty']
    ];
}
