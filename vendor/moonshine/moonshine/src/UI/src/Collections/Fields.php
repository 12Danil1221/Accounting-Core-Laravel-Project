<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\WithoutExtractionContract;
use MoonShine\Core\Collections\BaseCollection;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use MoonShine\UI\Contracts\FileableContract;
use MoonShine\UI\Fields\ID;
use Throwable;

/**
 * @template T of FieldContract
 * @implements FieldsContract<T>
 */
class Fields extends BaseCollection implements FieldsContract
{
    use Conditionable;

    /**
     * @param  FieldsContract|ComponentsContract|list<ComponentContract>  $elements
     * @param list<FieldContract> $data
     * @throws Throwable
     */
    protected function extractFields(iterable $elements, array &$data): void
    {
        foreach ($elements as $element) {
            if ($element instanceof FieldContract) {
                $data[] = $element;
            } elseif ($element instanceof HasFieldsContract && ! $element instanceof WithoutExtractionContract) {
                $this->extractFields($element->getFields(), $data);
            } elseif ($element instanceof HasComponentsContract && ! $element instanceof WithoutExtractionContract) {
                $this->extractFields($element->getComponents(), $data);
            }
        }
    }

    /**
     * @throws Throwable
     */
    public function onlyFields(bool $withWrappers = false): static
    {
        $data = [];

        $this->extractFields($this->toArray(), $data);

        /** @var static */
        return static::make($data)->when(
            ! $withWrappers,
            static fn (FieldsContract $fields): FieldsContract => $fields->withoutWrappers()
        );
    }

    /**
     * @throws Throwable
     */
    public function prepareAttributes(): static
    {
        /** @var static */
        return $this->onlyFields()
            ->map(
                static function (FieldContract $formElement): FieldContract {
                    $formElement->when(
                        ! $formElement instanceof FileableContract,
                        static function (FieldContract $field): void {
                            $field->mergeAttribute('x-on:change', 'onChangeField($event)', ';');
                        }
                    );

                    return $formElement;
                }
            );
    }

    public function unwrapElements(string $class): static
    {
        $modified = self::make();

        $this->each(
            static function ($element) use ($class, $modified): void {
                if ($element instanceof $class) {
                    $element->getFields()->each(
                        static fn ($inner): Collection => $modified->push($inner)
                    );
                } else {
                    $modified->push($element);
                }
            }
        );

        /** @var static */
        /** @noRector */
        return $modified;
    }

    public function withoutWrappers(): static
    {
        /** @var static */
        return $this->unwrapElements(FieldsWrapperContract::class);
    }

    /**
     * @throws Throwable
     */
    public function whenFields(): static
    {
        /** @var static */
        return $this->filter(
            static fn (FieldContract $field): bool => $field->hasShowWhen()
        )->values();
    }

    /**
     * @throws Throwable
     */
    public function whenFieldsConditions(): static
    {
        /** @var static */
        return $this->whenFields()->map(
            static fn (
                FieldContract $field
            ): array => $field->getShowWhenCondition()
        );
    }

    /**
     * @throws Throwable
     */
    public function fillCloned(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static {
        /** @var static */
        return ($preparedFields ?? $this->onlyFields())->map(
            static fn (FieldContract $field): FieldContract => (clone $field)
                ->fillData(\is_null($casted) ? $raw : $casted, $index)
        );
    }

    public function fillClonedRecursively(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): static {
        /** @var static */
        return ($preparedFields ?? $this)->map(static function (ComponentContract $component) use ($raw, $casted, $index): ComponentContract {
            if ($component instanceof HasFieldsContract) {
                $component = (clone $component)->fields(
                    $component->getFields()->fillClonedRecursively($raw, $casted, $index)
                );
            }

            if ($component instanceof FieldContract) {
                $component->fillData(\is_null($casted) ? $raw : $casted, $index);
            }

            return clone $component;
        });
    }

    /**
     * @throws Throwable
     */
    public function fill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): void
    {
        $this->onlyFields()->map(
            static fn (FieldContract $field): FieldContract => $field
                ->fillData(\is_null($casted) ? $raw : $casted, $index)
        );
    }

    /**
     * @throws Throwable
     */
    public function wrapNames(string $name): static
    {
        $this
            ->onlyFields()
            ->each(static fn (FieldContract $field): FieldContract => $field->wrapName($name));

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function reset(): void
    {
        $this->onlyFields()->map(
            static fn (FieldContract $field): FieldContract => $field->reset()
        );
    }

    public function onlyHasFields(): static
    {
        /** @var static */
        return $this->filter(static fn (FieldContract $field): bool => $field instanceof HasFieldsContract);
    }

    public function withoutHasFields(): static
    {
        /** @var static */
        return $this->filter(static fn (FieldContract $field): bool => ! $field instanceof HasFieldsContract);
    }

    /**
     * @param  ?callable(FieldContract,FieldContract): FieldsContract  $before
     * @param  ?callable(string,FieldContract,FieldContract): string  $performName
     *
     * @throws Throwable
     */
    public function prepareReindexNames(?FieldContract $parent = null, ?callable $before = null, ?callable $performName = null): static
    {
        /** @var static */
        return $this->map(static function (FieldContract $field) use ($parent, $before, $performName): FieldContract {
            $modifyField = \is_null($before) ? $field : $before($parent, $field);

            if ($modifyField instanceof FieldContract) {
                $field = $modifyField;
            }

            $name = str($parent ? $parent->getNameDot() : $field->getNameDot());

            $level = $name->substrCount('$');

            if ($field instanceof ID) {
                $field->showValue();
            }

            $name = $field->generateNameFrom(
                $name->value(),
                "\${index$level}",
                $parent ? $field->getColumn() : null,
            );

            if ($field->getAttribute('multiple') || $field->isGroup()) {
                $name .= '[]';
            }

            if ($parent) {
                $field
                    ->formName($parent->getFormName())
                    ->setParent($parent);
            }

            if ($parent && ! $field->hasWrapper()) {
                $field->customAttributes([
                    'x-id' => "[`field-{$parent->getFormName()}`]",
                ]);
            }

            return $field
                ->setNameAttribute(
                    \is_null($performName) ? $name : $performName($name, $parent, $field)
                )
                ->iterableAttributes($level);
        })
            ->prepareShowWhenNames();
    }

    public function prepareShowWhenNames(): static
    {
        /** @var static */
        return $this->map(static function (FieldContract $field): FieldContract {
            if (! $field->hasShowWhen()) {
                return $field;
            }

            $showWhenName = str($field->getIdentity())
                ->replace('_', '.')
                ->toString();

            return $field->modifyShowFieldName($showWhenName)
                ->customAttributes([
                    'data-show-when-field' => $showWhenName,
                ]);
        });
    }

    /**
     * @throws Throwable
     */
    public function reactiveFields(): static
    {
        /** @var static */
        return $this->filter(
            static fn (FieldContract $field): bool => $field->isReactive()
        );
    }

    /**
     * @return array<string, string>
     * @throws Throwable
     */
    public function extractLabels(): array
    {
        return $this->flatMap(
            static fn (FieldContract $field): array => [$field->getColumn() => $field->getLabel()]
        )->toArray();
    }

    /**
     * @throws Throwable
     */
    public function findByColumn(
        string $column,
        ?FieldContract $default = null
    ): ?FieldContract {
        return $this->first(
            static fn (FieldContract $field): bool => $field->getColumn() === $column,
            $default
        );
    }

    public function findByClass(
        string $class,
        ?FieldContract $default = null
    ): ?FieldContract {
        return $this->first(
            static fn (FieldContract $field): bool => $field::class === $class,
            $default
        );
    }
}
