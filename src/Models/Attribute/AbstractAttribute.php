<?php
declare(strict_types=1);

namespace App\Models\Attribute;

abstract class AbstractAttribute
{
    /**
     * @param AttributeItem[] $items
     */
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $type,
        protected array $items = []
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

}
