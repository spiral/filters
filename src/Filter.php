<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Filters;

use Spiral\Core\Traits\SaturateTrait;
use Spiral\Filters\Traits\ValidateTrait;
use Spiral\Models\SchematicEntity;
use Spiral\Validation\ValidatorInterface;

/**
 * Request filter is data entity which uses input manager to populate it's fields, model can
 * perform input filtering, value routing (query, data, files) and validation.
 *
 * Attention, you can not inherit one request from another at this moment. You can use generic
 * validation rules for your input fields.
 *
 * Please do not request instance without using container, constructor signature might change over
 * time (or another request filter class can be created with inheritance and composition support).
 *
 * Example schema definition:
 * const SCHEMA = [
 *       //identical to "data:name"
 *      'name'   => 'post:name',
 *
 *       //field name will used as search criteria in query ("query:field")
 *      'field'  => 'query',
 *
 *       //Yep, that's too
 *      'file'   => 'file:images.preview',
 *
 *       //Alias for InputManager->isSecure(),
 *      'secure' => 'isSecure'
 *
 *       //Iterate over file:uploads array with model UploadFilter and isolate it in uploads.*
 *      'uploads' => [UploadFilter::class, "uploads.*", "file:upload"],
 *
 *      //Nested model associated with address subset of data
 *      'address' => AddressRequest::class,
 *
 *       //Identical to previous definition
 *      'address' => [AddressRequest::class, "address"]
 * ];
 *
 * You can declare as source (query, file, post and etc) as source plus origin name (file:files.0).
 * Available sources: uri, path, method, isSecure, isAjax, isJsonExpected, remoteAddress.
 * Plus named sources (bags): header, data, post, query, cookie, file, server, attribute.
 */
class Filter extends SchematicEntity implements FilterInterface
{
    use SaturateTrait;
    use ValidateTrait {
        getErrors as fetchErrors;
    }

    // Default input source when nothing else is specified.
    public const DEFAULT_SOURCE = 'data';

    // Filter specific schema segments
    public const SH_MAP       = 0;
    public const SH_VALIDATES = 1;

    // Defines request data mapping (input => request property)
    public const SCHEMA = [];

    /** @var MapperInterface */
    private $mapper;

    /** @var array|null */
    private $mappedErrors = null;

    /**
     * @param InputInterface|null  $input
     * @param MapperInterface|null $mapper Scope based saturation.
     */
    public function __construct(InputInterface $input = null, MapperInterface $mapper = null)
    {
        $this->mapper = $this->saturate($mapper, MapperInterface::class);
        parent::__construct([], $mapper->getSchema($this));

        if (!empty($input)) {
            $this->mapper->initValues($this, $input);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setField(string $name, $value, bool $filter = true)
    {
        $this->errors = null;
        parent::setField($name, $value, $filter);
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
     * Get all validation messages (including nested models).
     *
     * @return array
     */
    public function getErrors(): array
    {
        if ($this->mappedErrors !== null && $this->errors !== null) {
            return $this->validateNested($this->mappedErrors);
        }

        //Making sure that each error point to proper input origin
        return $this->mappedErrors = $this->mapper->mapErrors($this, $this->fetchErrors());
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'valid'  => $this->isValid(),
            'fields' => $this->getFields(),
            'errors' => $this->getErrors()
        ];
    }

    /**
     * Validate entity.
     *
     * @return ValidatorInterface
     */
    protected function validate(): ValidatorInterface
    {
        return $this->mapper->validate($this, $this->context);
    }
}