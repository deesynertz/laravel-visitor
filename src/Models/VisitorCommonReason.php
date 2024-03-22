<?php

namespace Deesynertz\Visitor\Models;

use Illuminate\Database\Eloquent\Model;
use Deesynertz\Visitor\Models\VisitorLineItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorCommonReason extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function mainReason(): BelongsTo
    {
        return $this->belongsTo(VisitorCommonReason::class, 'parent_id', 'id')->withCount('subReasons as sub_reasons_count');
    }

    public function visitorLineItems(): HasMany
    {
        return $this->hasMany(VisitorLineItem::class, config('property-visitor.column_names')['visitor_reason_key'], 'id');
    }

    public function subReasons(): HasMany
    {
        return $this->hasMany(VisitorCommonReason::class, 'parent_id', 'id');
    }
}
