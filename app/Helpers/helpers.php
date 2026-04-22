<?php

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

function orderData( $value, $direction, $sort ) {
    if( $sort == $value )  {         
        if( $direction == 'desc' )
            return [
                'sort' =>  $sort,
                'direction' => $direction = 'asc'
            ];
        else{
            return [
                'sort' =>  $sort,
                'direction' => $direction = 'desc'
            ];
        }
            
    } else {
        return [
            'sort' => $value,
            'direction' => 'asc'
        ];     
    }
}

function toast($message, $action, $title = null, $position = 'top-end', $timer = 5000) {
    if($action == 'success')
        LivewireAlert::title($title ?? '')
        ->text($message)
        ->success()
        ->toast()
        ->position($position)
        ->timer($timer)
        ->show();

   else if ($action == 'info')
        LivewireAlert::title($title ?? '')
        ->text($message)
        ->info()
        ->toast()
        ->position($position)
        ->timer($timer)
        ->show();
   else
        LivewireAlert::title($title ?? '')
        ->text($message)
        ->error()
        ->toast()
        ->position($position)
        ->timer($timer)
        ->show();
}