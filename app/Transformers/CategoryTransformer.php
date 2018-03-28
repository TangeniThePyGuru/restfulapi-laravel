<?php

namespace App\Transformers;

use App\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param Category $category
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'identifier' => (string) $category->id,
            'title' => (string) $category->name,
            'detail' => (string) $category->description,
            'creationDate' => $category->created_at,
            'lastChange' => $category->updated_at,
            'deletedDate' => isset($category->deleted_at) ? (string) $category->deleted_at : null,

        ];
    }
}