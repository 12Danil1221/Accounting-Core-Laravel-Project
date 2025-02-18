<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;


class Post extends Model
{
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Заголовок', 'title')->required(),
            Textarea::make('Содержание', 'content')->required(),
        ];
    }
}