<?php

namespace Deesynertz\Visitor\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVistors
{
    public function propertyables(): MorphOne
    {
        return $this->morphOne(config('property-visitor.models.property_visitings'), 'propertyable', 'propertyable_type', 'propertyable_id', 'id');
    }
       
    public function visitorables($query):MorphMany
    {
        return $query->morphMany(config('property-visitor.models.visitor_line_items'), 'visitorable', 'visitorable_type', 'visitorable_id', 'id');
    }


    # For only User Model
    public function visitings($query): HasMany
    {
        return $query->hasMany(config('property-visitor.models.property_has_visitors'), 'user_id', 'id');
    }

    public function asCustodians($query): HasMany
    {
        return $query->HasMany(config('property-visitor.models.property_custodians'), 'user_id', 'id');
    }    
}