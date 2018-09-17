<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests\Fixtures;


use Spiral\Filters\Filter;

class ExternalFilter extends Filter
{
    const SCHEMA = [
        'key' => 'key'
    ];

    const VALIDATES = [
        'id' => ['notEmpty']
    ];
}