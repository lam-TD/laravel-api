<?php

namespace App\Modules\Version;

use Illuminate\Database\Eloquent\Model;

class VersionQuery
{
    protected int|string|Product $product;

    protected string $from;

    protected string $to;

    protected string $fromOperator;

    protected string $toOperator;

    public function __construct(protected Model $model) {}

    public static function fromModel(Model $model)
    {
        return new self($model);
    }

    public function belongToProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function from($name, $operator = '>=')
    {
        $this->from = $name;
        $this->fromOperator = $operator;

        return $this;
    }

    public function to($name, $operator = '<=')
    {
        $this->to = $name;
        $this->toOperator = $operator;

        return $this;
    }

    public function asc()
    {
        return $this->execute()->orderBy('order');
    }

    public function desc()
    {
        return $this->execute()->orderBy('order', 'desc');
    }

    protected function execute()
    {
        $model = $this->model->newQuery()->where('product_id', $this->product);

        if ($this->from) {
            $model->where('order', $this->fromOperator, function ($query) {
                $query
                    ->select('order')
                    ->from($this->model->getTable())
                    ->where('product_id', $this->product)->where('name', $this->from);
            });
        }

        if ($this->to) {
            $model->where('order', $this->toOperator, function ($query) {
                $query
                    ->select('order')
                    ->from($this->model->getTable())
                    ->where('product_id', $this->product)->where('name', $this->to);
            });
        }

        return $model;
    }
}
