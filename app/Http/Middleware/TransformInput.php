<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];
//        exchange the transformed inputs with the original once
        foreach ($request->request->all() as $input => $value){
            $transformedInput[$transformer::originalAttributes($input)] = $value;
        }
//        replace in inputs in the request and continue
        $request->replace($transformedInput);

        $response = $next($request);
//        act on the error response
        if (isset($response->exception) && $response->exception instanceof ValidationException){
            $data = $response->getData();

            $transformedErrors = [];

            foreach ($data->error as $field => $error){
                $transformedField = $transformer::transformedAttribute($field);

                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }

            $data->error = $transformedErrors;
//            intercept the data variable in the middleware and set the new data
            $response->setData($data);
        }

        return $response;




    }
}
