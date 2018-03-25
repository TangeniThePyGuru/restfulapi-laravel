<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        \App\User::truncate();
        \App\Category::truncate();
        \App\Product::truncate();
        \App\Transaction::truncate();
//      truncate the pivot table
        DB::table('category_product')->truncate();

//        disables event listeners for development
        \App\User::flushEventListeners();
        \App\Category::flushEventListeners();
        \App\Product::flushEventListeners();
        \App\Transaction::flushEventListeners();


        $usersQuantity = 1000;
        $categoriesQuantity = 30;
        $productsQuantity = 1000;
        $transactionsQuantity = 1000;


        factory(\App\User::class, $usersQuantity)->create();
        factory(\App\Category::class, $categoriesQuantity)->create();
        factory(\App\Product::class, $productsQuantity)->create()->each(function ($product){
            $categories = \App\Category::all()->random(mt_rand(1,5))->pluck('id');
            $product->categories()->attach($categories);
        });
        factory(\App\Transaction::class, $transactionsQuantity)->create();

    }
}
