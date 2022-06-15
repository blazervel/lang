<?php

namespace Blazervel\Lang\Providers;

use Blazervel\Lang\Lang;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BlazervelLangServiceProvider extends ServiceProvider 
{
  public function boot()
  {
    $this->loadDirectives();
  }

  private function loadDirectives(): void
  {
    Blade::directive('lang', fn ($group) => trim("
      <?php echo app('" . Lang::class . "')->generate({$group}) ?>
    "));
  }
}