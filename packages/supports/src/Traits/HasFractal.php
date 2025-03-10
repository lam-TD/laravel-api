<?php

namespace Ltd\Supports\Traits;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;

trait HasFractal
{
    
    protected bool $useMeta = true;
    protected string|null $resourceKey = null;

    abstract protected function transformer(): TransformerAbstract;

    protected function setMetaCollection($resource){
        return $resource;
    }

    protected function setMetaItem($resource)
    {
        return $resource;
    }

    protected function fractal($resource)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new \League\Fractal\Serializer\DataArraySerializer());
        // $fractal->parseIncludes(request()->get('include', ''));
        return $fractal->createData($resource);
    }

    protected function collection($data, $transformer, $isPaginated = true)
    {
        $collection = new Collection($data, $transformer, $this->resourceKey);

        if ($isPaginated) {
            $collection->setPaginator(new IlluminatePaginatorAdapter($data));
        }

        if ($this->useMeta) {
            $collection = $this->setMetaCollection($collection);
        }
        
        return $this->fractal($collection);
    }

    protected function item($data, $transformer)
    {
        $item = new Item($data, $transformer, $this->resourceKey);
        
        if ($this->useMeta) {
            $item = $this->setMetaItem($item);
        }
        
        return $this->fractal($item);
    }

}