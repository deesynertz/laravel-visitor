<?php

namespace Deesynertz\Visitor\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Deesynertz\Visitor\Models\VisitorLineItem;
use Deesynertz\Visitor\Models\PropertyVisiting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyCustodian extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dates = ['shift_start', 'shift_end'];
    protected $casts = ['status' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function guardProperty(): BelongsTo
    {
        return $this->belongsTo(PropertyVisiting::class, config('property-visitor.column_names')['property_visiting_key'], 'id');
    }

    public function visitorLineItems(): HasMany
    {
        return $this->hasMany(VisitorLineItem::class, config('property-visitor.column_names')['visitor_guard_key'], 'id');
    }

  
}


## usable

// ->PropertyCustodian() to get Property