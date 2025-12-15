<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
;

trait CanLoadRelationships
{
  public function loadRelationship(Model|EloquentBuilder|QueryBuilder $for, ?array $relations = null): Model|EloquentBuilder|QueryBuilder
  {
    $relations = $relations ?? $this->relations ?? [];

    foreach ($relations as $relation) {
      $for->when(
        $this->shouldIncludeRelation($relation),
        fn($q) => $for instanceof Model ? $q->load($relation) : $q->with($relation)
      );
    }

    return $for;
  }

  protected function shouldIncludeRelation(string $relation): bool
  {
    $include = request()->query('include');

    if (!$include) {
      return false;
    }

    $relations = array_map(fn($item) => trim($item), explode(',', $include));


    return in_array($relation, $relations);
  }
}