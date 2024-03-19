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

    function getAllVisitors($params = null, $perPage = null) {
        $results = $this->visitorsInstances()
            ->when(isset($params->property_type) && isset($params->property_ids), fn ($query) => 
            
                $query->whereHas('property', 
                    fn ($propertyQuery) => $propertyQuery->wherePropertyableType($params->property_type)
                        ->whereIn('propertyable_id', $params->property_ids)
                )
            );


        return responseBatch($results, $params, $perPage);
    }
    
}