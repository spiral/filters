<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\MemoryInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Validation\ValidatorInterface;

class InputMapper implements MapperInterface, SingletonInterface
{
    const MEMORY = 'filters';

    /** @var MemoryInterface */
    private $memory;

    /**
     * @inheritdoc
     */
    public function __construct(ClassesInterface $classes, MemoryInterface $memory)
    {
        $this->memory = $memory;
    }

    /**
     * @inheritdoc
     */
    public function initValues(FilterInterface $filter, InputInterface $input): array
    {
        // TODO: Implement initValues() method.
    }

    /**
     * @inheritdoc
     */
    public function mapErrors(FilterInterface $filter, array $errors): array
    {
        // TODO: Implement mapErrors() method.
    }

    /**
     * @inheritdoc
     */
    public function getSchema(FilterInterface $filter): array
    {
        // TODO: Implement getSchema() method.
    }

    /**
     * @inheritdoc
     */
    public function validate(FilterInterface $filter, $context = null): ValidatorInterface
    {
        // TODO: Implement validate() method.
    }


}