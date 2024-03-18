<?php

namespace Deesynertz\Visitor\Services;

use Deesynertz\Visitor\Traits\VistorServiceTrait;

class VisitorService
{
    use VistorServiceTrait;
     
    function getCommonReasons($flag = null) {
        return $this->commonReasons()
            ->when((!is_null($flag) || !constAll()), 
                fn ($query) => $query
                ->when($flag == 'parent', fn ($query) => $query->whereNull('parent_id'))
                ->when($flag == 'childs', fn ($query) => $query->whereNotNull('parent_id'))
            )
            ->get();
    }
    
}