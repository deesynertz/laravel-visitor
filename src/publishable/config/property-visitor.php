<?php

return [
    'models' => [
        'property_visitings' => Deesynertz\Visitor\Models\PropertyVisiting::class,
        'property_has_visitors' => Deesynertz\Visitor\Models\PropertyHasVisitor::class,
        'visitor_common_reasons' => Deesynertz\Visitor\Models\VisitorCommonReason::class,
        'property_custodians' => Deesynertz\Visitor\Models\PropertyCustodian::class,
        'visitor_line_items' => Deesynertz\Visitor\Models\VisitorLineItem::class,
    ],

    'table_names' => [
        'property_visitings' => 'property_visitings',
        'property_has_visitors' => 'property_has_visitors',
        'visitor_common_reasons' => 'visitor_common_reasons',
        'property_custodians' => 'property_custodians',
        'visitor_line_items' => 'visitor_line_items',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
        */
        'property_visiting_key' => 'property_visiting_id', //default 'property_visiting_id',
        'property_visitor_key' => 'property_visitor_id', //default 'property_has_visitor_id',
        'visitor_reason_key' => 'visitor_reason_id', //default 'visitor_common_reason_id',
        'property_custodian_key' => 'property_custodian_id', //default 'property_custodian_id',
        'visitor_line_item_key' => 'visitor_term_id', //default 'visitor_line_item_id',
    ],
];