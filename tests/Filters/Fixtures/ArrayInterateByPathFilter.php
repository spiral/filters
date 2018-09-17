<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests\Fixtures;


use Spiral\Filters\Filter;

class ArrayInterateByPathFilter extends Filter
{
    const SCHEMA = [
        'tests' => [TestFilter::class, "custom.*", 'by']
    ];
}