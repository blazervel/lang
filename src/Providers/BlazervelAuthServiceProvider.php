<?php

namespace Blazervel\Lang\Providers;

use Blazervel\Lang\Lang;
use Illuminate\Support\Facades\{ File, Blade, App, Lang };
use Illuminate\Support\{ Str, ServiceProvider };

class BlazervelAuthServiceProvider extends ServiceProvider 
{
  public function boot()
  {
    $this->loadDirectives();
  }

  private function loadDirectives(): void
  {
    Blade::directive('lang', fn ($group) => trim("
      <script type=\"text/javascript\"> 
        const Lang = <?php echo Js::from(['translations' => " . Lang::class . "::generate()]) ?>
      </script>
    "));
  }
}