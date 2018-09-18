<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Filters\Exception\MapperException;
use Spiral\Validation\Exceptions\ValidationException;
use Spiral\Validation\ValidatorInterface;

interface MapperInterface
{
    /**
     * Init entity values and nested filters.
     *
     * @param FilterInterface $filter
     * @param InputInterface  $input
     * @return mixed
     *
     * @throws MapperException
     */
    public function initValues(FilterInterface $filter, InputInterface $input);

    /**
     * Move entity errors into locations specific to entity schema.
     *
     * @param FilterInterface $filter
     * @param array           $errors
     * @return array
     *
     * @throws MapperException
     */
    public function mapErrors(FilterInterface $filter, array $errors): array;

    /**
     * Validate given filter entity.
     *
     * @param FilterInterface $filter
     * @param mixed           $context
     * @return ValidatorInterface
     *
     * @throws MapperException
     * @throws ValidationException
     */
    public function validate(FilterInterface $filter, $context = null): ValidatorInterface;

    /**
     * Get entity schema for specific filter.
     *
     * @param string $filter
     * @return array
     *
     * @throws MapperException
     */
    public function getSchema(string $filter): array;
}