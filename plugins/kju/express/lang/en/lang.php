<?php return [
    'plugin' => [
        'name' => 'KJU Express',
        'description' => 'KJU Express',
    ],
    'permission' => [
        'access_branches' => 'Manage branches',
        'tab' => 'KJU Express',
        'access_services' => 'Manage services',
        'access_delivery_routes' => 'Manage delivery routes',
        'access_delivery_costs' => 'Manage delivery costs',
        'access_check_delivery_cost' => 'Access check delivery cost',
        'access_users' => 'Manage users',
        'access_delivery_orders' => 'Manage delivery orders',
        'access_status_types' => 'Manage status types',
        'access_regions' => 'Manage regions',
        'access_statuses' => 'Manage delivery order statuses',
    ],
    'branch' => [
        'id' => 'Branch Id',
        'plural' => 'Branches',
        'name' => 'Branch Name',
        'singular' => 'Branch',
        'code' => 'Branch Code',
        'new' => 'New Branch',
        'edit' => 'Edit Branch',
        'preview' => 'Preview Branch',
    ],
    'global' => [
        'region_data' => 'Region Data',
        'description' => 'Description',
        'id' => 'Id',
        'origin' => 'Origin',
        'destination' => 'Destination',
        'destination_type' => 'Destination Type',
        'cost' => 'Cost',
        'total_cost' => 'Total Cost',
        'not_available' => 'Not Available',
        'additional_cost' => 'Additional Cost',
        'region' => 'Region',
        'weight' => 'Weight (KG)',
        'check_delivery_cost' => 'Check Delivery Cost',
        'master_data' => 'Master Data',
        'province' => 'Province',
        'regency' => 'Regency',
        'district' => 'District',
        'kg' => 'KG',
        'lead_time' => 'Lead Time',
        'days' => 'Days',
        'route_code' => 'Route Code',
        'phone_number' => 'Phone Number',
        'status' => 'Status',
        'detail_price' => 'Detail Price',
        'agree_total_cost' => 'Agreement for total delivery cost',
        'email' => 'Email',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'created_by' => 'Created By',
        'updated_by' => 'Updated By',

        'pickup' => 'Pick Up',
        'process' => 'Process',
        'transit' => 'Transit',
        'received' => 'Received',
        'failed' => 'Failed',
        'delete_not_allowed' => 'Delete not allowed',
        'print' => 'Print'
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
        'new' => 'New Service',
        'edit' => 'Edit Service',
        'preview' => 'Preview Service',
        'weight_limit' => 'Weight Limit (KG)',
        'weight_limit_comment' => '-1 for unlimited weight',
    ],
    'delivery_route' => [
        'plural' => 'Delivery Routes',
        'singular' => 'Delivery Route',
        'new' => 'New Delivery Route',
        'edit' => 'Edit Delivery Route',
        'preview' => 'Preview Delivery Route',
        'code' => 'Route Code',
    ],
    'delivery_cost' => [
        'plural' => 'Delivery Costs',
        'singular' => 'Delivery Cost',
        'comment_cost' => 'Basic cost for limited weight',
        'comment_add_cost' => 'Additional cost after exceeding the limit',
        'check' => 'Check Delivery Cost',
        'min_lead_time' => 'Lead Time (Min)',
        'max_lead_time' => 'Lead Time (Max)',
        'lead_time' => 'Lead Time',
    ],
    'user' => [
        'plural' => 'Users',
        'singular' => 'User',
        'new' => 'New User',
        'edit' => 'Edit User',
        'preview' => 'Preview User',
    ],
    'delivery_order' => [
        'plural' => 'Delivery Orders',
        'singular' => 'Delivery Order',
        'code' => 'Order Code',
        'pickup_request' => 'Pickup Request',
        'pickup_data' => 'Pickup Data',
        'pickup_date' => 'Date (Pickup)',
        'pickup_address' => 'Address (Pickup)',
        'pickup_postal_code' => 'Postal Code (Pickup)',
        'consignee_address' => 'Address (Consignee)',
        'consignee_postal_code' => 'Postal Code (Consignee)',
        'consignee_phone_number' => 'Phone Number (Consignee)',
        'branch_region' => 'Region (Branch)',
        'pickup_region' => 'Region (Pickup)',
        'consignee_region' => 'Region (Consignee)',
        'shipper_data' => 'Shipper Data',
        'consignee_data' => 'Consignee Data',
        'goods_data' => 'Goods Data',
        'goods_description' => 'Description (Goods)',
        'goods_amount' => 'Amount (Goods)',
        'goods_weight' => 'Weight (Goods)',
        'pickup_courier' => 'Courier (Pickup)',
        'new' => 'New Delivery Order',
        'edit' => 'Edit Delivery Order',
        'preview' => 'Preview Delivery Order',
        'consignee_name' => 'Name (Consignee)',
    ],
    'status' => [
        'plural' => 'Statuses',
        'singular' => 'Status',
    ],
    'courier' => [
        'plural' => 'Couriers',
    ],
    'region' => [
        'id' => 'Region Id',
        'plural' => 'Regions',
        'singular' => 'Region',
        'name' => 'Region Name',
        'type' => 'Region Type',
        'parent' => 'Region Parent',
        'new' => 'New Region',
        'edit' => 'Edit Region',
        'preview' => 'Preview Region',
    ],
    'customer' => [
        'singular' => 'Customer',
        'id' => 'Customer Id',
        'name' => 'Customer Name',
    ],
    'deliver_order_status' => [
        'plural' => 'Delivery Order Statuses',
        'singular' => 'Delivery Order Status',
        'import' => 'Import Delivery Order Status',
    ],
];