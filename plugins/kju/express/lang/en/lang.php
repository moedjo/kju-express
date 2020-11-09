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
    ],
    'branch' => [
        'id' => 'Branch Id',
        'plural' => 'Branches',
        'name' => 'Branch Name',
        'singular' => 'Branch',
        'region' => 'Region',
    ],
    'global' => [
        'region_data' => 'Region Data',
        'description' => 'Description',
        'select_an_option' => '-- select an option --',
        'id' => 'Id',
        'destination' => 'Destination',
        'cost' => 'Cost',
        'additional_cost' => 'Additional Cost',
    ],
    'district' => [
        'plural' => 'Districts',
        'id' => 'District Id',
        'name' => 'District Name',
    ],
    'regency' => [
        'plural' => 'Regencies',
        'id' => 'Regency Id',
        'name' => 'Regency Name',
        'singular' => 'Regency',
    ],
    'province' => [
        'plural' => 'Provincies',
        'id' => 'Province Id',
        'name' => 'Province Name',
        'singular' => 'Province',
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
    ],
    'delivery_route' => [
        'plural' => 'Delivery Routes',
    ],
    'delivery_cost' => [
        'plural' => 'Delivery Costs',
        'singular' => 'Delivery Cost',
        'comment_cost' => 'Limit 10kg for BSC (Basic) service',
        'comment_add_cost' => 'Additional cost per kg after exceeding the limit',
    ],
];