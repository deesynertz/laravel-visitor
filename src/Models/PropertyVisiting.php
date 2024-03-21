<?php

namespace Deesynertz\Visitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PropertyVisiting extends Model
{
    use HasFactory;
    protected $guarded  = ['id'];
    protected $casts = ['status' => 'boolean'];

    public function propertyable(): MorphTo
    {
        return $this->morphTo('propertyable', 'propertyable_type', 'propertyable_id', 'id');
    }

    public function visitorTerms(): HasManyThrough
    {
        return $this->hasManyThrough(
                app(config('property-visitor.models.visitor_line_items')),
                app(config('property-visitor.models.property_has_visitors')),
                config('property-visitor.column_names.property_visiting_key'), 
                config('property-visitor.column_names.property_visitor_key'), 
                'id',
                'id'
            );
    }

    public function hasVisitors(): HasMany
    {
        return $this->hasMany(
            config('property-visitor.models.property_has_visitors'), 
            config('property-visitor.column_names.property_visiting_key'), 
            'id'
        )
        ->withCount(['visitorLineItems as visitor_line_counts']);;
    }

    public function propertyCustodians(): HasMany
    {
        return $this->hasMany( 
            app(config('property-visitor.models.property_custodians')),
            config('property-visitor.column_names.property_visiting_key'), 
            'id'
        );
    }
    

    ## CRUID 
    public function scopeWhereHasPropertyable($query, $params) {
        return $query->has('propertyable')
            ->when(isset($params->property_type) && isset($params->property_ids), fn ($query) => 
               $query->wherePropertyableType($params->property_type)->whereIn('propertyable_id', $params->property_ids)
            );
    }   

    
}
