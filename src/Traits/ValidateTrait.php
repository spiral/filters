<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters\Traits;

use Spiral\Filters\FilterInterface;
use Spiral\Translator\Traits\TranslatorTrait;
use Spiral\Translator\Translator;
use Spiral\Validation\ValidatorInterface;

trait ValidateTrait
{
    use TranslatorTrait;

    /** @var array|null */
    private $errors = null;

    /** @var mixed */
    private $context = null;

    /**
     * @inheritdoc
     */
    public function setContext($context)
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
     * @param string|null $field
     * @return bool
     */
    public function hasErrors(string $field = null): bool
    {
        if (empty($field)) {
            return !$this->isValid();
        }

        return !empty($this->getErrors()[$field]);
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
        if ($this->errors !== null) {
            return $this->validateNested($this->errors);
        }

        $this->errors = $this->validate()->getErrors();
        foreach ($this->errors as &$error) {
            if (is_string($error) && Translator::isMessage($error)) {
                // translate error message
                $error = $this->say($error);
            }

            unset($error);
        }

        return $this->validateNested($this->errors);
    }

    /**
     * Force re-validation.
     */
    public function reset()
    {
        $this->errors = null;
    }

    /**
     * Validate entity.
     *
     * @return ValidatorInterface
     */
    abstract protected function validate(): ValidatorInterface;

    /**
     * Validate inner entities.
     *
     * @param array $errors
     *
     * @return array
     */
    private function validateNested(array $errors): array
    {
        foreach ($this->getFields(false) as $index => $value) {
            if (isset($errors[$index])) {
                //Invalid on parent level
                continue;
            }

            if ($value instanceof FilterInterface && !$value->isValid()) {
                $errors[$index] = $value->getErrors();
                continue;
            }

            //Array of nested entities for validation
            if (is_array($value) || $value instanceof \Traversable) {
                foreach ($value as $nIndex => $nValue) {
                    if ($nValue instanceof FilterInterface && !$nValue->isValid()) {
                        $errors[$index][$nIndex] = $nValue->getErrors();
                    }
                }
            }
        }

        return $errors;
    }
}