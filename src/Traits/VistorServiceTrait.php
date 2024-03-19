<?php

namespace Deesynertz\Visitor\Traits;

use Deesynertz\Visitor\Models\PropertyHasVisitor;
use Deesynertz\Visitor\Models\VisitorCommonReason;

trait VistorServiceTrait
{
    function commonReasons() {
        return VisitorCommonReason::query();
    }

    function createOrUpdatePropertyInVisitor($target, $action) {
        $callback = (object)[
            'status' => false,
            'code' => 404,
            'message' => '',
        ];

        if ($action == 'add') {
            # create
            $callback->status = createdInstance($target->propertyable()->firstOrCreate());
            if ($callback->status) {
                $callback->code = 200;
            }
        } else {
            # change
            // $propertyable = $target->propertyable;
            
            // if () {
                
            // }
            // $callback->status = $propertyable->update();

            // if ($callback->status) {
            //     $callback->code = 201;
            // }
        }

        return $callback;
    }



    # visitors
    function visitorsInstances() {
        return PropertyHasVisitor::with('property')
            ->with('user')
            ->with('visitorLineItems')
            ->withCount('visitorLineItems as visiting_count');
    }
        
}