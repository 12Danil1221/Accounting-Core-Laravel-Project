<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\RemovableContract;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Traits\Fields\HasVerticalMode;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Removable;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<TableBuilderContract|FieldsGroup>
 */
class Json extends Field implements
    HasFieldsContract,
    FieldWithComponentContract,
    RemovableContract,
    HasDefaultValueContract,
    CanBeArray
{
    use WithFields;
    use Removable;
    use WithDefaultValue;
    use HasVerticalMode;

    protected string $view = 'moonshine::fields.json';

    protected bool $keyValue = false;

    protected bool $onlyValue = false;

    protected bool $objectMode = false;

    protected bool $isGroup = true;

    protected bool $isCreatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButtonContract $creatableButton = null;

    protected array $buttons = [];

    protected bool $isReorderable = true;

    protected bool $isFilterMode = false;

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyRemoveButton = null;

    protected bool $resolveValueOnce = true;

    protected null|TableBuilderContract|FieldsGroup $resolvedComponent = null;

    /**
     * @throws Throwable
     */
    public function keyValue(
        string $key = 'Key',
        string $value = 'Value',
        ?FieldContract $keyField = null,
        ?FieldContract $valueField = null,
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;

        $this->fields([
            ($keyField ?? Text::make($key, 'key'))
                ->setColumn('key')
                ->customAttributes($this->getAttributes()->jsonSerialize()),

            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
                ->customAttributes($this->getAttributes()->jsonSerialize()),
        ]);

        return $this;
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }

    /**
     * @throws Throwable
     */
    public function onlyValue(
        string $value = 'Value',
        ?FieldContract $valueField = null,
    ): static {
        $this->keyValue = false;
        $this->onlyValue = true;

        $this->fields([
            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
                ->customAttributes($this->getAttributes()->jsonSerialize()),
        ]);

        return $this;
    }

    public function isOnlyValue(): bool
    {
        return $this->onlyValue;
    }

    /**
     * @throws Throwable
     */
    public function object(): static
    {
        $this->objectMode = true;

        return $this->customAttributes([
            'class' => 'space-elements',
        ]);
    }

    public function isObjectMode(): bool
    {
        return $this->objectMode;
    }

    public function isKeyOrOnlyValue(): bool
    {
        return $this->keyValue || $this->onlyValue;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?int $limit = null,
        ?ActionButtonContract $button = null,
    ): static {
        $this->isCreatable = value($condition, $this) ?? true;

        if ($this->isCreatable()) {
            $this->creatableLimit = $limit;
            $this->creatableButton = $button?->customAttributes([
                '@click.prevent' => 'add()',
            ]);
        }

        return $this;
    }

    public function getCreateButton(): ?ActionButtonContract
    {
        return $this->creatableButton;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function getCreateLimit(): ?int
    {
        return $this->creatableLimit;
    }

    public function filterMode(): static
    {
        $this->isFilterMode = true;
        $this->creatable(false);

        return $this;
    }

    public function isFilterMode(): bool
    {
        return $this->isFilterMode;
    }

    public function reorderable(Closure|bool|null $condition = null): static
    {
        $this->isReorderable = value($condition, $this) ?? true;

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->isReorderable;
    }

    /**
     * @param  Closure(TableBuilder $table, bool $preview): TableBuilder  $callback
     */
    public function modifyTable(Closure $callback): self
    {
        $this->modifyTable = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyRemoveButton(Closure $callback): self
    {
        $this->modifyRemoveButton = $callback;

        return $this;
    }

    public function buttons(array $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): array
    {
        if (array_filter($this->buttons) !== []) {
            return $this->buttons;
        }

        $buttons = [];

        if ($this->isRemovable()) {
            $button = ActionButton::make('', '#')
                ->icon('trash')
                ->onClick(static fn ($action): string => 'remove', 'prevent')
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-error'])
                ->showInLine();

            if (! \is_null($this->modifyRemoveButton)) {
                $button = \call_user_func($this->modifyRemoveButton, $button, $this);
            }

            $buttons[] = $button;
        }

        return $buttons;
    }

    protected function prepareFields(): FieldsContract
    {
        if ($this->isObjectMode()) {
            return $this->getFields()
                ->wrapNames($this->getColumn())
                ->map(fn ($field) => $field->customAttributes($this->getReactiveAttributes("{$this->getColumn()}.{$field->getColumn()}")))
            ;
        }

        return $this->getFields()
            ->prepareAttributes()
            ->prepareReindexNames(parent: $this, before: static function (self $parent, FieldContract $field): void {
                $field
                    ->withoutWrapper()
                    ->setRequestKeyPrefix($parent->getRequestKeyPrefix());
            });
    }

    protected function resolveRawValue(): mixed
    {
        return (string) $this->rawValue;
    }

    protected function resolvePreview(): Renderable|string
    {
        $component = $this->getComponent();

        // FieldsGroup component
        if ($this->isObjectMode()) {
            return $component
                ->previewMode()
                ->render();
        }

        return $component
            ->simple()
            ->preview()
            ->render();
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        if ($this->isKeyOrOnlyValue() && ! $this->isFilterMode()) {
            return collect($data)->map(fn ($data, $key): array => $this->extractKeyValue(
                $this->isOnlyValue() ? [$data] : [$key => $data],
            ))->values()->toArray();
        }

        return $data;
    }

    protected function extractKeyValue(array $data): array
    {
        if ($this->isKeyValue()) {
            return [
                'key' => key($data) ?? '',
                'value' => $data[key($data)] ?? '',
            ];
        }

        if ($this->isOnlyValue()) {
            return [
                'value' => $data[key($data)] ?? '',
            ];
        }

        return $data;
    }

    protected function isBlankValue(): bool
    {
        if ($this->isPreviewMode()) {
            return parent::isBlankValue();
        }

        return blank($this->value);
    }

    /**
     * @throws Throwable
     */
    public function prepareOnApplyRecursive(iterable $collection): array
    {
        $collection = $this->prepareOnApply($collection);

        foreach ($this->getFields() as $field) {
            if ($field instanceof self) {
                foreach ($collection as $index => $value) {
                    $column = $field->getColumn();
                    $collection[$index][$column] = $field->prepareOnApplyRecursive(
                        $value[$column] ?? []
                    );
                }
            }
        }

        return $collection;
    }

    /**
     * @throws Throwable
     */
    protected function resolveOldValue(mixed $old): mixed
    {
        return $this->prepareOnApplyRecursive($old);
    }

    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        $value = $this->isPreviewMode()
            ? $this->toFormattedValue()
            : $this->getValue();

        $values = Collection::make(
            is_iterable($value)
                ? $value
                : [],
        );

        $fields = $this->getPreparedFields();

        if ($this->isObjectMode()) {
            return FieldsGroup::make(
                $fields,
            )->fill($values->toArray())->mapFields(
                fn (FieldContract $field): FieldContract => $field
                    ->formName($this->getFormName())
                    ->setParent($this),
            );
        }

        $values = $values->when(
            ! $this->isPreviewMode() && ! $this->isCreatable() && blank($values),
            static fn ($values): Collection => $values->push([null]),
        );

        $reorderable = ! $this->isPreviewMode() && $this->isReorderable();

        if ($reorderable) {
            $fields->prepend(
                Preview::make(
                    column: '__handle',
                    formatted: static fn () => Icon::make('bars-4'),
                )->customAttributes(['class' => 'handle', 'style' => 'cursor: move']),
            );
        }

        $component = TableBuilder::make($fields, $values)
            ->name('repeater_' . $this->getColumn())
            ->inside('field')
            ->customAttributes(
                $this->getAttributes()
                    ->except(['class', 'data-name', 'data-column'])
                    ->when(
                        $reorderable,
                        static fn (ComponentAttributesBagContract $attr): ComponentAttributesBagContract => $attr->merge([
                            'data-handle' => '.handle',
                        ]),
                    )
                    ->jsonSerialize()
            )
            ->customAttributes(['data-validation-wrapper' => true])
            ->when(
                $reorderable,
                static fn (TableBuilderContract $table): TableBuilderContract => $table->reorderable(),
            )
            ->when(
                $this->isVertical(),
                fn (TableBuilderContract $table): TableBuilderContract => $table->vertical(
                    title: $reorderable ? fn (FieldContract $field, ComponentContract $default): Column => Column::make([
                        $field->getColumn() === '__handle' ? $field : Div::make([
                            $field->getLabel(),
                        ]),
                    ])->columnSpan($this->verticalTitleSpan) : null,
                    value: $reorderable ? fn (FieldContract $field, ComponentContract $default): Column => $field->getColumn() === '__handle'
                        ? Column::make()->columnSpan($this->verticalValueSpan)
                        /** @var Column $default */
                        /** @phpstan-ignore-next-line  */
                        : $default->columnSpan($this->verticalValueSpan)->customAttributes(['data-validation-wrapper' => true]) : null,
                ),
            )
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilder $tableBuilder) => value($this->modifyTable, $tableBuilder, $this->isPreviewMode()),
            );

        if (! $this->isPreviewMode()) {
            $component = $component
                ->editable()
                ->reindex(prepared: true)
                ->when(
                    $this->isCreatable(),
                    fn (TableBuilderContract $table): TableBuilderContract => $table->creatable(
                        limit: $this->getCreateLimit(),
                        button: $this->getCreateButton(),
                    )->removeAfterClone(),
                )
                ->buttons($this->getButtons())
                ->simple();
        }

        return $this->resolvedComponent = $component;
    }

    /**
     * @throws Throwable
     */
    public function prepareOnApply(iterable $collection): array
    {
        $collection = collect($collection);

        return $collection->when(
            $this->isKeyOrOnlyValue(),
            fn ($data): Collection => $data->mapWithKeys(
                fn ($data, $key): array => $this->isOnlyValue()
                    ? [$key => $data['value']]
                    : [$data['key'] => $data['value']],
            ),
        )->filter(fn ($value): bool => $this->filterEmpty($value))->toArray();
    }

    private function filterEmpty(mixed $value): bool
    {
        if (is_iterable($value) && filled($value)) {
            return collect($value)
                ->filter(fn ($v): bool => $this->filterEmpty($v))
                ->isNotEmpty();
        }

        return ! blank($value);
    }

    /**
     * @throws Throwable
     */
    protected function resolveAppliesCallback(
        mixed $data,
        Closure $callback,
        ?Closure $response = null,
        bool $fill = false,
    ): mixed {
        $requestValues = array_filter($this->getRequestValue() ?: []);
        $applyValues = [];

        if ($this->isObjectMode()) {
            $requestValues = [$requestValues];
        }

        foreach ($requestValues as $index => $values) {
            foreach ($this->resetPreparedFields()->getPreparedFields() as $field) {
                if (! $field->isCanApply()) {
                    continue;
                }

                if (! $this->isObjectMode()) {
                    $field->setNameIndex($index);
                }

                $field->when($fill, static fn (FieldContract $f): FieldContract => $f->fillData($values));

                $apply = $callback($field, $values, $data);

                data_set(
                    /** @phpstan-ignore-next-line  */
                    $applyValues[$index],
                    $field->getColumn(),
                    data_get($apply, $field->getColumn()),
                );
            }
        }

        $preparedValues = $this->prepareOnApply($applyValues);
        $values = $this->isKeyValue() ? $preparedValues : array_values($preparedValues);

        if ($this->isObjectMode()) {
            $values = $values[0] ?? [];
        }

        return \is_null($response) ? data_set(
            $data,
            str_replace('.', '->', $this->getColumn()),
            $values,
        ) : $response($values, $data);
    }

    protected function resolveOnApply(): ?Closure
    {
        return fn ($item): mixed => $this->resolveAppliesCallback(
            data: $item,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->apply(
                static fn ($data): mixed => data_set($data, $field->getColumn(), data_get($values, $field->getColumn(), '')),
                $values,
            ),
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->beforeApply($values),
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->afterApply($values),
            response: static fn (array $values, mixed $data): mixed => $data,
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $values = $this->toValue(withDefault: false);

        if (! $this->isKeyOrOnlyValue() && filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        static fn (FieldContract $field): mixed => $field
                            ->fillData($value)
                            ->afterDestroy($value),
                    );
            }
        }

        return $data;
    }

    public function getReactiveValue(): mixed
    {
        if (! $this->isObjectMode()) {
            throw FieldException::reactivityNotSupported(static::class, 'without object mode');
        }

        return $this->toValue() ?? $this->getPreparedFields()
            ->onlyFields()
            ->mapWithKeys(fn (FieldContract $field) => [$field->getColumn() => null]);
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'component' => $this->getComponent(),
        ];
    }
}
