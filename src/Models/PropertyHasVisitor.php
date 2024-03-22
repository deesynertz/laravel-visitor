<?php

namespace Deesynertz\Visitor\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyHasVisitor extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function getPropertyableAttribute() { return $this->property->propertyable; }
    
    public function property(): BelongsTo
    {
        return $this->belongsTo(PropertyVisiting::class, config('property-visitor.column_names.property_visiting_key'), 'id');
    }

    public function user(): BelongsTo
    {
        // $user = app(config('property-visitor.models.users'));
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function visitorLineItems(): HasMany
    {
        return $this->hasMany(
                app(config('property-visitor.models.visitor_line_items')), 
                config('property-visitor.column_names.property_visitor_key'),
                'id'
            )
            ->with('visitorable');
    }

    public function visitorLineItem(): HasOne
    {
        return $this->hasOne(
                app(config('property-visitor.models.visitor_line_items')), 
                config('property-visitor.column_names.property_visitor_key'), 
                'id'
            )
            ->with('visitorable')
            ->with('visitingReason')
            ->latest('id');
    }

    public function scopeWhereHasProperty($query, $params) {
        return $query->whereHas('property',  fn ($propertyQuery) => $propertyQuery->whereHasPropertyable($params));
    }

}
