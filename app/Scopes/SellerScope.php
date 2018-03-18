<?php
/**
 * Created by PhpStorm.
 * User: tangeni
 * Date: 3/19/18
 * Time: 12:35 AM
 */

namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SellerScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $builder->has('products');
    }

}