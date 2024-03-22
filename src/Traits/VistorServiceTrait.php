<?php

namespace Deesynertz\Visitor\Traits;

use Deesynertz\Visitor\Models\VisitorLineItem;
use Deesynertz\Visitor\Models\PropertyVisiting;
use Deesynertz\Visitor\Models\PropertyHasVisitor;
use Deesynertz\Visitor\Models\VisitorCommonReason;

trait VistorServiceTrait
{
    # Property
    function visitableProperies() {
        return PropertyVisiting::with('propertyable')
            ->with(['hasVisitors' => fn ($query) => $query->orderBy('created_at', 'DESC')])
            ->with('propertyCustodians')
            ->withCount(['hasVisitors as visitors_counts'])
            ->withCount(['propertyCustodians as property_custodian_count']);
    }


    function commonReasons() {
        return VisitorCommonReason::query();
    }

    function createOrUpdatePropertyInVisitor($target, $action, $values = []) {
        $callback = httpResponseAttr();

        if ($action == 'add') {
            # create
            if (!isset($values['status'])) {
                $values = addElement($values, ['status' => true]);
            }

            if (!empty($values)) {
                $callback->status = createdInstance($target->propertyable()->firstOrCreate($values));
            }

            if ($callback->status) {
                $callback->code = 200;
            }
        } else {
            # change
            $propertyable = $target->propertyable;
            $values = addElement($values, ['status' => !$propertyable->status]);

            if (!empty($values)) {
                $callback->status = $propertyable->update($values);
            }

            if ($callback->status) {
                $callback->code = 201;
            }
        }

        return $callback;
    }




    # visitors
    function visitorsInstances() {
        return PropertyHasVisitor::with('property')
            ->with('user')
            ->with('visitorLineItems')
            ->withCount(['visitorLineItems as visiting_count']);
    }

    function storeOrRetrievePropertyHasVisitor(PropertyVisiting $propertyVisiting, $values) {
        return $propertyVisiting->hasVisitors()->firstOrCreate($values);
    }

    function storeVisitorLineItems(PropertyHasVisitor $propertyHasVisitor, $values) {
        return $propertyHasVisitor->visitorLineItems()->create($values);
    }

    function findVisitorLineItem($id) {
        return VisitorLineItem::find($id);
    }

    function updateVisitorLineItems(VisitorLineItem $visitorLineItem, $values) {
        return $visitorLineItem->update($values);
    }
        
}