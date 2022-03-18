<?php

namespace Blazervel\Blazervel;

use Blazervel\Blazervel\Exceptions\BlazervelOperationException;

abstract class Operation
{

  abstract protected function steps(): array;

  protected mixed $latestResponse;

  public function run(): mixed {

    foreach ($this->steps() as $stepMethod) :
      $latestResponse = $this->$stepMethod();
    endforeach;

    return $this->latestResponse = $latestResponse;
  }

}