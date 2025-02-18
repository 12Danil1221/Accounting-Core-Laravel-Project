<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Pages\TagIndexPage;
use App\MoonShine\Pages\TagFormPage;
use App\MoonShine\Resources\AdvantagesResource;
use App\MoonShine\Resources\TagDetailPageResource;
use App\MoonShine\Pages\TagDetailPage;
use App\MoonShine\Resources\PostResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(CoreContract $core, ConfiguratorContract $config): void
    {
         $config
         ->authEnable()->title('My Application')
         ->logo('/assets/logo.png')
         ->prefixes('admin', 'page', 'resource')
         ->guard('moonshine')
         ->authEnable()
         ->useMigrations()
         ->useNotifications()
         ->useDatabaseNotifications()
         ->middleware([
             // ...
         ])
         ->layout(\MoonShine\Laravel\Layouts\AppLayout::class);

        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}