<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Filters\Traits;

use Spiral\Translator\Traits\TranslatorTrait;
use Spiral\Translator\Translator;
use Spiral\Validation\ValidatorInterface;

trait ValidateTrait
{
    use TranslatorTrait;

    /** @var mixed */
    private $context = null;

    /**
     * @inheritdoc
     */
    public function setContext($context): void
    {
        $this->context = $context;
        $this->reset();
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return empty($this->getErrors());
    }

    /**
     * Get all validation messages.
     *
     * @return array
     */
    public function getErrors(): array
    {
        $errors = $this->validate()->getErrors();
        foreach ($errors as &$error) {
            if (is_string($error) && Translator::isMessage($error)) {
                // translate error message
                $error = $this->say($error);
            }

            unset($error);
        }

        return $errors;
    }

    /**
     * Force re-validation.
     */
    abstract public function reset();

    /**
     * Validate entity.
     *
     * @return ValidatorInterface
     */
    abstract protected function validate(): ValidatorInterface;
}
