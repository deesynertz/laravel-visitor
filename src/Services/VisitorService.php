<?php

namespace Deesynertz\Visitor\Services;

use Illuminate\Support\Facades\DB;
use Deesynertz\Visitor\Models\PropertyVisiting;
use Deesynertz\Visitor\Traits\VistorServiceTrait;

class VisitorService
{
    use VistorServiceTrait;
     
    function getCommonReasons($flag = null, $id = null) {
        return $this->commonReasons()
            ->when((!is_null($flag) || !constAll()), 
                fn ($query) => $query
                ->when($flag == 'parent', fn ($query) => $query->whereNull('parent_id'))
                ->when($flag == 'childs', fn ($query) => $query->whereNotNull('parent_id'))
            )
            ->when(!is_null($id), 
                fn ($results) => $results->find($id),
                fn ($results) => responseBatch($results, ['flag' => $flag], null)
            );
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
        return $this->visitableProperies()
            ->whereHasPropertyable($params)
            ->find($params->id);
    }

    function getVisitablePropertyByCode($params) {
        return $this->visitableProperies()
            ->whereHasPropertyable($params)
            ->wherePropertyCode($params->property_code)
            ->first();
    }

    /**
     */

    function handleVisitingAction(PropertyVisiting $propertyVisiting, $user, $visitingItems) {
        # create of get visiting if exist of the same person and house

        $feedback = httpResponseAttr();
        DB::beginTransaction();
        try {
            $hasVisitor = $this->storeOrRetrievePropertyHasVisitor($propertyVisiting, ['user_id' => $user->id]);
            if (createdInstance($hasVisitor) && !empty($visitingItems)) {
                # add to the visitingItems
                if ($feedback->status = createdInstance($this->storeVisitorLineItems($hasVisitor, $visitingItems))) {
                    DB::commit();
                    $feedback->code = 200;
                    $feedback->content = $hasVisitor;
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $feedback->message = $th->getMessage();
        }

        return $feedback;
    }

    
    
}