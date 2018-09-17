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

        return $this->validateNested($errors);
    }

    /**
     * Validate entity.
     *
     * @return ValidatorInterface
     */
    abstract protected function validate(): ValidatorInterface;

    /**
     * Force re-validation.
     */
    abstract public function reset();

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