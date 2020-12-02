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
        'singular' => 'Cabang',
        'code' => 'Kode Cabang',
        'new' => 'New Branch',
        'edit' => 'Edit Branch',
        'preview' => 'Preview Branch',
    ],
    'global' => [
        'region_data' => 'Region Data',
        'description' => 'Description',
        'id' => 'Id',
        'origin' => 'Origin',
        'destination' => 'Tujuan',
        'destination_type' => 'Destination Type',
        'cost' => 'Harga',
        'total_cost' => 'Total Harga',
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
        'phone_number' => 'Nomor Telepon',
        'status' => 'Status',
        'detail_price' => 'Detail Price',
        'agree_total_cost' => 'Pastikan total harga pengiriman',
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
        'code' => 'Resi',
        'pickup_request' => 'Pickup Request',
        'pickup_data' => 'Data Pickup',
        'pickup_date' => 'Tanggal Pickup',
        'pickup_address' => 'Alamat Pickup',
        'pickup_postal_code' => 'Kode Pos',
        'consignee_address' => 'Alamat Penerima',
        'consignee_postal_code' => 'Kode Pos',
        'consignee_phone_number' => 'Nomor Telepon Penerima',
        'branch_region' => 'Wilayah (Cabang)',
        'pickup_region' => 'Wilayah (Pickup)',
        'consignee_region' => 'Tujuan Pengiriman',
        'shipper_data' => 'Data Pengirim',
        'consignee_data' => 'Data Penerima',
        'goods_data' => 'Data Barang',
        'goods_description' => 'Keterangan',
        'goods_amount' => 'Koli',
        'goods_weight' => 'Berat (kg)',
        'pickup_courier' => 'Kurir Pickup',
        'new' => 'New Delivery Order',
        'edit' => 'Edit Delivery Order',
        'preview' => 'Preview Delivery Order',
        'consignee_name' => 'Name Penerima',
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
        'singular' => 'Pengirim',
        'id' => 'Customer Id',
        'name' => 'Nama Pengirim',
    ],
    'deliver_order_status' => [
        'plural' => 'Delivery Order Statuses',
        'singular' => 'Delivery Order Status',
        'import' => 'Import Delivery Order Status',
    ],
];