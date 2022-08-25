<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

function customPaginate($items, $perPage, $options = null,  $page = null)
    {
        if($options && count($options) < 1) {
            $options = [];
        }
        // dd($options);
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $user_array = new LengthAwarePaginator($items->forPage($page, $perPage), count($items), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()], $options);
        
        // dd($user_array);
        return $user_array;
        // return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, ['path' => LengthAwarePaginator::resolveCurrentPath()], $page, $options);
    }
