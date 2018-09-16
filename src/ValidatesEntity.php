<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Validation\ValidatorInterface;

class ValidatesEntity
{
    /** @var ValidationInterface */
    private $validation;

    /** @var mixed */
    private $context = null;

    /** @var array|null */
    private $errors = null;

    /**
     * {@inheritdoc}
     */
    public function setField(string $name, $value, bool $filter = true)
    {
        $this->errors = null;

        return parent::setField($name, $value, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($offset)
    {
        $this->errors = null;

        parent::__unset($offset);
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setContext($context)
    {
        $this->context = $context;
        $this->errors = null;
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Validate entity.
     *
     * @return ValidatorInterface
     */
    protected function validate(): ValidatorInterface
    {
        return $this->validation->validate($this, [], $this->context);
    }

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