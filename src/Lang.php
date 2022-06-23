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

        function lang(key, replace = {}, locale = '{{ app()->getLocale() }}', fallback = true) {
          const { translations } = typeof BlazervelLang !== 'undefined' ? BlazervelLang : globalThis?.BlazervelLang,
                keys = key.split('.');

          let translation = null,
              localeTranslations = translations[locale] || {}

          keys.map(k => translation = localeTranslations[k] || '');

          if (!translation && fallback) {
            for (var localeKey in translations) {
              keys.map(k => translation = translations[localeKey][k] || '');

              if (translation) {
                break;
              };
            };
          };

          if (translation) {
            for (var key in replace) {
              translation = translation.replace(':' + key, replace[key]);
            };
          };
        
          return translation || key;
        };

        window.lang = lang;
      </script>
    ");
  }

  private function translations(): array
  {
    $translationFiles = File::allFiles(
      lang_path()
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