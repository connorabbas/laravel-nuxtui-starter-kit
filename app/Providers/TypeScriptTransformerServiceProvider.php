<?php

namespace App\Providers;

use App\Support\TypeScript\AttributedEnumTransformer;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelTypeScriptTransformer\LaravelData\LaravelDataTypeScriptTransformerExtension;
use Spatie\LaravelTypeScriptTransformer\RouteFilters\NamedRouteFilter;
use Spatie\LaravelTypeScriptTransformer\TransformedProviders\LaravelRouteTransformedProvider;
use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider as BaseTypeScriptTransformerServiceProvider;
use Spatie\TypeScriptTransformer\Formatters\EslintFormatter;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Writers\GlobalNamespaceWriter;

class TypeScriptTransformerServiceProvider extends BaseTypeScriptTransformerServiceProvider
{
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        $config
            ->extension(new LaravelDataTypeScriptTransformerExtension())
            ->transformer(AttributedEnumTransformer::class)
            ->transformDirectories(app_path('Data'), app_path('Enums'))
            ->outputDirectory(resource_path('js/types'))
            ->writer(new GlobalNamespaceWriter('generated.d.ts'))
            ->formatter(EslintFormatter::class)
            ->provider(new LaravelRouteTransformedProvider(
                filters: [new NamedRouteFilter('boost.*', 'index')],
                path: '../utils/route.ts',
                absoluteUrlsByDefault: false,
            ))
            ->replaceType(Carbon::class, 'string')
            ->replaceType(CarbonImmutable::class, 'string');
    }
}
