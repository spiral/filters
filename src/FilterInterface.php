<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Models\EntityInterface;

interface FilterInterface extends EntityInterface
{
    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * Associate context with the filter (available in validator).
     *
     * @param mixed $context
     */
    public function setContext($context);

    /**
     * Associate context with the filter (available in validator).
     *
     * @return mixed
     */
    public function getContext();


}