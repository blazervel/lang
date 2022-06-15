<?php

namespace Blazervel\Lang;

use Illuminate\Support\Facades\{ File, App, Lang as LaravelFacade };
use Illuminate\Support\{ Js, Str };

class Lang
{
  public function generate(): string
  {
    $translations = $this->translations();
    $js = Js::from(['translations' => $translations]);

    return trim("
      <script id=\"blazervel_lang\" type=\"text/javascript\">
        const BlazervelLang = {$js};
      </script>
    ");
  }

  private function translations(): array
  {
    $translationFiles = File::files(
      lang_path(
        App::currentLocale()
      )
    );

    $langKey = fn ($file) => (
      Str::remove(".{$file->getExtension()}", $file->getFileName())
    );

    return (
      collect($translationFiles)
        ->map(fn ($file) => [$langKey($file) => LaravelFacade::get($langKey($file))])
        ->collapse()
        ->all()
    );
  }
}