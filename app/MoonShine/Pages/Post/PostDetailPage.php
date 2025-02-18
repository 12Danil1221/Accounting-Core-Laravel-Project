<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Post;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use Throwable;

class PostDetailPage extends DetailPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */

     protected ?string $layout = AppLayout::class;

     protected string $title = 'CustomPage';
 
     protected string $subtitle = 'Подзаголовок';

     public function getTitle(): string
     {
         return $this->title ?: 'CustomPage';
     }
  
     public function getSubtitle(): string
     {
         return $this->subtitle ?: 'Подзаголовок';
     }

     protected function components(): iterable
    {
        return [
            Grid::make([
                Column::make([
                    Box::make([
                        // ...
                    ])
                ])->columnSpan(6),
                Column::make([
                    Box::make([
                        // ...
                    ])
                ])->columnSpan(6),
            ])
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }
}