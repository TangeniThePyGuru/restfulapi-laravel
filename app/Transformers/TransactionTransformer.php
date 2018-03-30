<?php

namespace App\Transformers;

use App\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     * @param Transaction $transaction
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        return [
            'identifier' => (int)$transaction->id,
            'quantity' => (int)$transaction->quantity,
            'buyer' => (int)$transaction->buyer_id,
            'product' => (int)$transaction->product_id,
            'creationDate' => (string) $transaction->created_at,
            'lastChange' => (string) $transaction->updated_at,
            'deletedDate' => isset($transaction->deleted_at) ? (string)$transaction->deleted_at : null,

        ];
    }

    /**
     * @param $index
     * @return mixed|null
     *
     * Maps the transformed attribute names to their original attribute names
     */

    public static function originalAttributes($index){

        $attributes = [
            'identifier' => 'id',
            'quantity' => 'quantity_id',
            'buyer' => 'buyer_id',
            'product' => 'product_id',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;

    }
}
