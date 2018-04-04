<?php
/**
 * Created by PhpStorm.
 * User: tangeni
 * Date: 3/18/18
 * Time: 1:53 AM
 */

namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiResponser
{
    /**
     * @param $data
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse($data, $code){
        return response()->json($data, $code);
    }

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    protected function errorResponse($message, $code){
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    /**
     * @param Collection $collection
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function showAll(Collection $collection, $code = 200){

        if ($collection->isEmpty()){
            return $this->successResponse(['data' => $collection],$code);
        }

        $transformer = $collection->first()->transformer;
        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);

        return $this->successResponse($collection,$code);
    }

    /**
     * @param Model $instance
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */

    protected function showOne(Model $instance, $code = 200){

        $transformer = $instance->transformer;

        $instance = $this->transformData($instance, $transformer);

        return $this->successResponse($instance,$code);
    }

    /**
     * @param $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */

    protected function showMessage($message, $code = 200){
        return $this->successResponse(['data' => $message],$code);
    }

    /**
     * @param Collection $collection
     * @param $transformer
     * @return Collection|static
     */

    protected function filterData(Collection $collection, $transformer){
//        loops through every query parameter
        foreach (request()->query() as $query => $value){
            $attribute = $transformer::originalAttributes($query);
//            if the attribute and the value are set
            if (isset($attribute, $value)){
                $collection = $collection->where($attribute, $value);
            }
        }

        return $collection;

    }

    /**
     * @param Collection $collection
     * @return Collection|static
     */

    protected function sortData(Collection $collection, $transformer){

        if (request()->has('sort_by')){
            $attribute = $transformer::originalAttributes(request()->sort_by);

            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function paginate(Collection $collection){
        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 15;

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page,[
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    /**
     * @param $data
     * @param $transformer
     * @return array
     */
    protected function transformData($data, $transformer){
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }
}