<?php return [
    'plugin' => [
        'name' => 'KJU Express',
        'description' => 'KJU Express',
    ],
    'permission' => [
        'access_branches' => 'Manage Branches',
        'tab' => 'KJU Express',
        'access_provinces' => 'Manage Provinces',
        'access_regencies' => 'Manage Regencies',
        'access_districts' => 'Manage Districts',
        'access_services' => 'Manage Services',
        'access_delivery_routes' => 'Manage Delivery Routes',
        'access_delivery_costs' => 'Manage Delivery Costs',
        'access_check_delivery_cost' => 'Access Check Delivery Cost',
        'access_users' => 'Manage Users',
    ],
    'branch' => [
        'id' => 'Branch Id',
        'plural' => 'Branches',
        'name' => 'Branch Name',
        'singular' => 'Branch',
        'code' => 'Branch Code',
        'new_branch' => 'New Branch',
        'edit_branch' => 'Edit Branch',
        'preview_branch' => 'Preview Branch',
    ],
    'global' => [
        'region_data' => 'Region Data',
        'description' => 'Description',
        'id' => 'Id',
        'source' => 'Source',
        'destination' => 'Destination',
        'cost' => 'Cost',
        'total_cost' => 'Total Cost',
        'not_available' => 'Not Available',
        'additional_cost' => 'Additional Cost',
        'region' => 'Region',
        'source_or_branch' => 'Source / Branch',
        'weight' => 'Weight (KG)',
        'check_delivery_cost' => 'Check Delivery Cost',
    ],
    'district' => [
        'plural' => 'Districts',
        'id' => 'District Id',
        'name' => 'District Name',
        'singular' => 'District',
        'new_district' => 'New District',
        'edit_district' => 'Edit District',
        'preview_district' => 'Preview District',
    ],
    'regency' => [
        'plural' => 'Regencies',
        'id' => 'Regency Id',
        'name' => 'Regency Name',
        'singular' => 'Regency',
        'new_regency' => 'New Regency',
        'edit_regency' => 'Edit Regency',
        'preview_regency' => 'Preview Regency',
    ],
    'province' => [
        'plural' => 'Provincies',
        'id' => 'Province Id',
        'name' => 'Province Name',
        'singular' => 'Province',
        'new_province' => 'New Province',
        'edit_province' => 'Edit Province',
        'preview_province' => 'Preview Province',
    ],
    'delivery' => [
        'plural' => 'Deliveries',
        'data' => 'Delivery Data',
    ],
    'service' => [
        'code' => 'Service Code',
        'plural' => 'Services',
        'singular' => 'Service',
        'name' => 'Service Name',
        'new_service' => 'New Service',
        'edit_service' => 'Edit Service',
        'preview_service' => 'Preview Service',
        'weight_limit' => 'Weight Limit (KG)',
        'weight_limit_comment' => '-1 for unlimited weight',
    ],
    'delivery_route' => [
        'plural' => 'Delivery Routes',
        'new_delivery_route' => 'New Delivery Route',
        'edit_delivery_route' => 'Edit Delivery Route',
        'preview_delivery_route' => 'Preview Delivery Route',
    ],
    'delivery_cost' => [
        'plural' => 'Delivery Costs',
        'singular' => 'Delivery Cost',
        'comment_cost' => 'Basic cost for limited weight',
        'comment_add_cost' => 'Additional cost after exceeding the limit',
        'check' => 'Check Delivery Cost',
        'min_lead_time' => 'Lead Time (Min)',
        'max_lead_time' => 'Lead Time (Max)',
    ],
    'user' => [
        'plural' => 'Users',
    ],
];