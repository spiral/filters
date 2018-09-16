<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Tests;

use Spiral\Filters\Tests\Fixtures\TestFilter;

class SchemasTest extends BaseTest
{
    public function testSchemas()
    {
        $schema = $this->getMapper()->getSchema(TestFilter::class);
        $this->assertNotEmpty($schema);
        dumP($schema);
    }
}