<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Filters;

use Spiral\Models\Reflection\ReflectionEntity;
use Spiral\Tokenizer\ClassesInterface;

final class FilterLocator implements LocatorInterface
{
    /** @var ClassesInterface */
    private $classes;

    /**
     * @param ClassesInterface $classes
     */
    public function __construct(ClassesInterface $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @inheritdoc
     */
    public function locateFilters(): \Generator
    {
        foreach ($this->classes->getClasses(Filter::class) as $filter) {
            if ($filter->isAbstract()) {
                continue;
            }

            yield new ReflectionEntity($filter->getName());
        }
    }
}
