<?php

namespace App\Modules\Product;

class ProductRule
{
    public static function forUpdateLogo()
    {
        return [
            'logo' => 'required_if:is_delete_logo,null|file|mimes:jpeg,png,jpg|max:2048',
            'is_delete_logo' => 'in:0,1|required_if:logo,null',
        ];
    }

    public static function forLogo()
    {
        return [
            'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
