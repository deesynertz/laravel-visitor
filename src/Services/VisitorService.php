<?php

namespace Deesynertz\Visitor\Services;

use Deesynertz\Visitor\Traits\VistorServiceTrait;

class VisitorService
{
    use VistorServiceTrait;
     
    function getCommonReasons($flag = null) {
        $results = $this->commonReasons()
            ->when((!is_null($flag) || !constAll()), 
                fn ($query) => $query
                ->when($flag == 'parent', fn ($query) => $query->whereNull('parent_id'))
                ->when($flag == 'childs', fn ($query) => $query->whereNotNull('parent_id'))
            );

        return responseBatch($results, ['flag' => $flag], null);
    }

    function getAllVisitors($params = null, $perPage = null) {
        $results = $this->visitorsInstances()->whereHasProperty($params);
        return responseBatch($results, $params, $perPage);
    }

    function getVisitableProperties($params = null, $perPage = null) {
        $results = $this->visitableProperies()->whereHasPropertyable($params);
        return responseBatch($results, $params, $perPage);
    }

    function getVisitablePropertyById($params) {
        return $this->visitableProperies()->whereHasPropertyable($params)
            ->find($params->id);

    }
    
}