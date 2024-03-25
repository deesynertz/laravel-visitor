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

    # scope
    public function scopeWhereDateQuery($query, $params)
    {
        
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
