<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Validation\ValidatorInterface;

interface MapperInterface
{
    public function initValues(FilterInterface $filter, InputInterface $input);

    public function mapErrors(FilterInterface $filter, array $errors): array;


    public function validate(FilterInterface $filter, $context = null): ValidatorInterface;

    public function getSchema(FilterInterface $filter): array;
}