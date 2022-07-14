<?php

namespace Blazervel\Lang\Providers;

use Blazervel\Lang\Lang;
use Blazervel\Lang\Tr;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\{ Str, Collection };
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class BlazervelLangServiceProvider extends ServiceProvider 
{
  public function register()
  {
    // $this->app->booting(fn () => $this->registerClassAliases());
  }

  public function boot()
  {
    $this->loadDirectives();
  }

  private function registerClassAliases(): Collection
  {
    $loader = AliasLoader::getInstance();
    $extends = Tr::class;
    $prefix = Tr::dynamicClassPrefix;

    return (
      $this->namespaces()->map(function ($namespace) use ($loader, $extends, $prefix) {
          
        // Dynamically extending Tr class
        eval("class {$namespace}{$prefix} extends {$extends} { public string \$namespace = ''; }");

        $loader->alias(
          "Tr\\{$namespace}",
          "{$namespace}{$prefix}"
        );

      })
    );
  }

  private function namespaces(): Collection
  {
    $langFiles = File::allFiles(
        lang_path()
    );

    return collect($langFiles)->map(fn ($file) => (
        Str::ucfirst(
            Str::camel(
                Str::remove(
                    search: ".{$file->getExtension()}",
                    subject: $file->getFileName()
                )
            )
        )
    ));
  }

  private function loadDirectives(): void
  {
    Blade::directive('blazervelLang', fn ($group) => trim("
      <?php echo app('" . Lang::class . "')->generate({$group}) ?>
    "));
  }
}