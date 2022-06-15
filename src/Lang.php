<?php

namespace Blazervel\Lang;

use Illuminate\Support\Facades\{ File, App, Lang as LaravelFacade };
use Illuminate\Support\Str;

class Lang
{
  static function generate(): array
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