<?php

namespace Deesynertz\Visitor\Models;

use Illuminate\Database\Eloquent\Model;
use Deesynertz\Visitor\Models\PropertyCustodian;
use Deesynertz\Visitor\Models\PropertyHasVisitor;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyVisiting extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];

    public function propertyable(): MorphTo
    {
        return $this->morphTo('propertyable', 'propertyable_type', 'propertyable_id', 'id');
    }

    public function hasVisitors(): HasMany
    {
        return $this->hasMany(PropertyHasVisitor::class, config('property-visitor.column_names')['property_visiting_key'], 'id');
    }

    public function propertyCustodians(): HasMany
    {
        return $this->hasMany(PropertyCustodian::class, config('property-visitor.column_names')['property_visiting_key'], 'id');
    }
    

    ## CRUID 

    
}
