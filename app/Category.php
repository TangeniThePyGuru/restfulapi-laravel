<?php

namespace App;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [ 'name', 'description'];

    /**
     * @var array
     *
     * hides the pivot table
     */
    protected $hidden = ['pivot'];
    /**
     * @var string
     */
    public $transformer = CategoryTransformer::class;

    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
