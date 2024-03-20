<?php

namespace Deesynertz\Visitor\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Deesynertz\Visitor\Models\VisitorLineItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyHasVisitor extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(PropertyVisiting::class, config('property-visitor.column_names')['property_visiting_key'], 'id');
    }

    public function user(): BelongsTo
    {
        // $user = app(config('property-visitor.models.users'));
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function visitorLineItems(): HasMany
    {
        return $this->hasMany(VisitorLineItem::class, config('property-visitor.column_names')['property_visitor_key'], 'id');
    }

    public function scopeWhereHasProperty($query, $params) {
        return $query->whereHas('property',  fn ($propertyQuery) => $propertyQuery->whereHasPropertyable($params));
    }

}
