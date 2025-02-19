<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Advantages;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Advantages>
 */
class AdvantagesResource extends ModelResource
{
    protected string $model = Advantages::class;

    protected string $title = 'Статистика компании';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
                ID::make()->sortable(),
                Text::make('Count','Advantages_integer')->sortable(),
                Text::make('Содержание','Advantages_description')->sortable(),
            
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
                ID::make()->sortable(),
                Text::make('Count','Advantages_integer')->sortable(),
                Text::make('Содержание', 'Advantages_description')->sortable()->required(),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Count','Advantages_integer')->sortable(),
            Text::make('Description','Advantages_description')->sortable(),
        ];
    }

    /**
     * @param Advantages $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'Advantages_integer' => ['required'],
            'Advantages_description' => ['required'],
        ];
    }
    protected function search(): array
    {
        return [
            'id',
            'Advantages_integer',
            'Advantages_description',
        ];
    }
}