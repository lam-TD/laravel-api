<?php

namespace Ltd\Supports\Traits;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

trait HasQueryBuilder {
  protected array $allowedSorts = [];
  protected array $allowedFilters = [];
  protected array $allowedIncludes = [];
  protected array $allowedFields = [];

  protected array $allowedFilterScopes = [];
  protected array $allowedFilterExtracts = [];

  public function newQueryBuilder($model, ?Request $request = null)
  {
    return QueryBuilder::for($model, $request)
      ->allowedSorts($this->allowedSorts)
      ->allowedFilters($this->loadFilters())
      ->allowedFields($this->allowedFields)
      ->allowedIncludes($this->allowedIncludes);
  }

  public function loadFilters()
  {
    return array_merge($this->allowedFilters, 
    $this->allowedFilterScopes,
    // array_map(fn($scope) => AllowedFilter::scope($scope), $this->allowedFilterScopes),
    array_map(fn($extract) => AllowedFilter::exact($extract), $this->allowedFilterExtracts));
  }
}