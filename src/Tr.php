<?php

namespace Blazervel\Lang;

use SplFileInfo;
use Illuminate\Support\{ Str, Collection };
use Illuminate\Support\Facades\{ File, Lang };

class Tr
{
    const dynamicClassPrefix = 'DynamicTranslationHelper';

    public string $namespace;

    public function handle(
        string $namespace,
        string $key,
        array $replace = null,
        int|array|Collection $number = null,
        string $locale = null,
        bool $fallback = null
    ) {

        if ($fallback === null) :
            $fallback = true;
        endif;

        if ($locale === null) :
            $locale = Lang::getLocale();
        endif;

        if (!Lang::has("{$namespace}.{$key}")) :

            $defaultValue = Str::ucfirst(
                Str::replace('_', ' ', Str::snake($key))
            );

            return $defaultValue;

            // $filePath = lang_path(
            //     Str::lower("{$locale}/{$namespace}.php")
            // );

            // $fileContents = File::get($filePath);
            // $fileContents = Str::replaceLast(']', PHP_EOL . "'{$key}' => '{$defaultValue}',", $fileContents);

            // File::put(
            //     $filePath,
            //     $fileContents
            // );

        endif;

        if ($number !== null) :

            $number = is_array($number) ? count($number) : (is_numeric($number) ? $number : $number->count());

            return Lang::choice(
                key: "{$namespace}.{$key}",
                number: $number,
                replace: $replace ?: [],
                locale: $locale
            );

        endif;

        return Lang::get(
            key: "{$namespace}.{$key}",
            replace: $replace ?: [],
            locale: $locale,
            fallback: $fallback ?: true
        );
    }

    static function __callStatic($name, $arguments)
    {
        return (new self)->handle(
            namespace: Str::snake(class_basename(Str::remove(self::dynamicClassPrefix, get_called_class()))),
            key:       $name,
            replace:   $arguments['replace'] ?? $arguments[0] ?? null,
            number:    $arguments['number']  ?? $arguments[1] ?? null,
            locale:    $arguments['locale']  ?? $arguments[2] ?? null,
            fallback:  $arguments['replace'] ?? $arguments[3] ?? null
        );
    }
}