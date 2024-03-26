<?php

namespace Deesynertz\Visitor\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Deesynertz\Visitor\Models\VisitorLineItem;
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
        $results = $this->visitorsInstances()
            ->whereHasProperty($params)
            ->when(isset($params->filter_by), fn ($query) => 
                $query->when(($params->filter_by == strPropertyOwner()), fn ($query) => 
                    $query
                )
                ->when(isset($params->main_reason), fn ($query) => $query
                    ->whereHas('visitorLineItems', fn ($lineItemsQuery) => $lineItemsQuery->whereVisitorReasonId($params->main_reason))
                )
            );

        return responseBatch($results, $params, $perPage);
    }

    function getVisitorLineItems($params = null, $perPage = null) {
        $results = $this->visitorLineItemObj()
            ->with('visitorable')
            ->with('visitingReason')
            ->with('propertyCustodian')
            ->with(['propertyHasVisitor' => fn ($query) => $query->whereHasProperty($params)])
            ->whereParamsQuery($params);

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
    
    function visitingInfoById($id) {
        $callback = null;

        if (!is_null($visitorLineItem = $this->findVisitorLineItem($id))) {
            $propertyHasVisitor = $visitorLineItem->propertyHasVisitor;

            $userInfo = [
                'name' => ($user = $propertyHasVisitor->user)->name,
                'phone' => $user->phone,
                'imageUrl' => $user->avatar,
            ];

            $callback = addElement($callback, $userInfo);

            $houseInfo = [
                'house_no' => displayBlockNumber($propertyHasVisitor->propertyable, false),
                'unit_no' => !is_null($visitorable = $visitorLineItem->visitorable) ? displayBlockNumber($visitorable):null,
            ];
            $callback = addElement($callback, $houseInfo);

            $visitingInstance = [
                'starting' => $visitorLineItem->starting,
                'reason' => $visitorLineItem->content
            ];
            $callback = addElement($callback, $visitingInstance);
        }

        return $callback;

    }

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

    function handleVisitorLineItemUpdate($target, $visitingItems) {
        $feedback = httpResponseAttr();
        DB::beginTransaction();
        try {
            $visitorLineItem = $target instanceof VisitorLineItem ? $target : $this->findVisitorLineItem($target);

            if (!is_null($visitorLineItem)) {
                $feedback->status = $this->updateVisitorLineItems($visitorLineItem, $visitingItems);
                $visitorLineItem->refresh();
                DB::commit();
                $feedback->code = 200;
                $feedback->content = $visitorLineItem;
                $feedback->message = 'successfuly!';
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $feedback->message = $th->getMessage();
        }

        return $feedback;
    }

    public function acticateVisitorModeByDefault($target)
    {
        $values = ['property_code' => propertyCodeHelper()];
        $visitingMode = $this->createOrUpdatePropertyInVisitor($target, 'add', $values);
        
        if (!$visitingMode->status) {
            ## notify user that the visiting mode faild to be activated
        }

        $visitingMode;
    }
    
    public function autoSignoutVisitor($endingTime = null)
    {
        $ending = is_null($endingTime) ? now():$endingTime;
        $values = ['auto_signout' => true];
        $sixHoursBack = Carbon::create($ending)->subHours(6);
        $affectedRow = 0;

        $this->getVisitorLineItems((object)['query' => true])
            ->select('id','starting','ending')
            ->whereDate('starting','<=', $sixHoursBack)
            ->whereNull('ending')
            ->chunk(20, function ($visitorLineItems) use ($values, &$affectedRow, $endingTime) {
                foreach ($visitorLineItems as $visitorLineItem) {
                    $values = array_merge($values, ['ending' => is_null($endingTime) ? now():Carbon::create($endingTime)->addSeconds(35)]);
                    $feedback = $this->handleVisitorLineItemUpdate($visitorLineItem, $values);
                    if ($feedback->status) {
                        $affectedRow += 1;
                    }
                }
            });
            
        return $affectedRow;
    }
    
}