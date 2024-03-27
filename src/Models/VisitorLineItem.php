<?php

namespace Deesynertz\Visitor\Models;

use Illuminate\Database\Eloquent\Model;
use Deesynertz\Visitor\Models\PropertyCustodian;
use Deesynertz\Visitor\Models\PropertyHasVisitor;
use Deesynertz\Visitor\Models\VisitorCommonReason;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorLineItem extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];
    protected $dates = ['starting', 'ending'];
    protected $casts    = [
        'auto_signout'   => 'boolean'
    ];

    public function getPropertyableAttribute() { return $this->propertyHasVisitor->propertyable; }
    public function getContentAttribute() { return $this->visitingReason->content; }
    public function getReasonParentIdAttribute() { return $this->visitingReason->parent_id; }

    # scope
    public function scopeWherePropertyHasVisitor($query, $params)
    {
        return $query->whereHas('propertyHasVisitor', fn ($query) => $query->whereHasProperty($params));
    }

    public function scopeWhereParamsQuery($query, $params)
    {
        $mainReason = isset($params->main_reason) ? $params->main_reason:null;
        $starting = null;
        $ending = null;
        
        if (isset($params->date_between)) {
            $dateBetween = isset($params->date_between) ? $params->date_between:null;
            $starting = isset($dateBetween[0]) ? $dateBetween[0]:$starting;
            $ending = isset($dateBetween[0]) ? $dateBetween[1]:$ending;
        }

        return $query->wherePropertyHasVisitor($params)
            ->when(!is_null($mainReason), fn ($query) => $query->where(fn ($query) => 
                $query->whereVisitorReasonId($mainReason)->orWhereHas('visitingReason', fn ($visitingReason) => $visitingReason->whereParentId($mainReason))
            ))
            ->where(fn ($query) =>  $query
                ->when(!is_null($starting), fn ($query) => $query->whereDate('starting', '>=', $starting))
                ->when(!is_null($ending), fn ($query) => $query->whereDate('ending', '<=', $ending))
            );
    }


    public function visitorable(): MorphTo
    {
        return $this->morphTo('visitorable', 'visitorable_type', 'visitorable_id', 'id');
    }

    public function propertyHasVisitor(): BelongsTo
    {
        return $this->belongsTo(PropertyHasVisitor::class, config('property-visitor.column_names')['property_visitor_key'], 'id');
    }

    public function visitingReason(): BelongsTo
    {
        return $this->belongsTo(VisitorCommonReason::class, config('property-visitor.column_names')['visitor_reason_key'], 'id');
    }

    public function propertyCustodian(): BelongsTo
    {
        return $this->belongsTo(PropertyCustodian::class, config('property-visitor.column_names')['property_custodian_key'], 'id');
    }
}
