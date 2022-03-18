<?php

namespace Blazervel\Blazervel;

use Blazervel\Blazervel\Exceptions\BlazervelContractException;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorResponse;

abstract class Contract
{

  abstract protected function rules(): array;

  public array $rules;
  public array $only;
  public string $modelNamespace = 'App\\Models';

  public function __construct(array $data, array $rules = null, string|array $only = null)
  {
    $this->data = $data;

    $this->rules = $this->rules();

    if ($rules) :
      $this->rules = array_merge($this->rules, $rules);
    endif;

    if ($this->only = $only) :
      $this->rules = collect($this->rules)->only($only)->all();
    endif;

    $this->model();
  }

  public function model(): void
  {
    $className = class_basename($this);

    if (!Str::contains('Contract', $className)) :
      throw new BlazervelContractException(
        "You've improperly named your contract. The convention should be `{modelName}Contract`."
      );
    endif;

    $modelClassName = Str::remove('Contract', $className);
    $modelProperty = Str::camel($modelClassName);
    $modelClass = "{$this->modelNamespace}\\{$modelClassName}";

    // Set model based on validator class name
    $this->$modelProperty = new $modelClass;
  }

  public static function make(array $data, array $rules = null, array $only = null): ValidatorResponse
  {
    $className = get_called_class();
    $validator = new $className($data, $rules, $only);

    return Validator::make(
      $validator->data, 
      $validator->rules, 
      $validator->only
    );
  }
  
}