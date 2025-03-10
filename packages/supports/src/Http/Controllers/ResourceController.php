<?php

namespace Ltd\Supports\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Ltd\Supports\Traits\HasFractal;
use Ltd\Supports\Traits\HasQueryBuilder;
use App\Http\Controllers\Finance\Transformer\PersonalSpedingActitityTransformer;

abstract class ResourceController
{
    use HasFractal;
    use HasQueryBuilder;
    protected bool $usePagination = true;
    protected Request $request;

    abstract public function model(): string|Model;

    public function resourceCollection()
    {
        $builder = $this->newQueryBuilder($this->model(), $this->request);
        $fractal = $this->collection($builder->paginate(), $this->transformer());
        return $fractal;
    }

    public function resourceItem($id)
    {
        $builder = $this->newQueryBuilder($this->model(), $this->request);
        $fractal = $this->item($builder->find($id), $this->transformer());
        return $fractal;
    }

    public function setMetaCollection($resource)
    {
        $resource->setMeta([
            'available_fields' => $this->allowedFields,
            'available_includes' => $this->allowedIncludes,
            'available_sorts' => $this->allowedSorts,
            'available_filters' => [
                ...$this->allowedFilters,
                ...$this->allowedFilterScopes,
            ],
        ]);

        return $resource;
    }

    public function setMetaItem($resource)
    {
        $resource->setMeta([
            'available_fields' => $this->allowedFields,
            'available_includes' => $this->allowedIncludes,
        ]);

        return $resource;
    }
}
