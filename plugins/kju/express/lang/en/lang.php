<?php return [
    'plugin' => [
        'name' => 'Putra KJU Express',
        'description' => 'Putra KJU Express',
    ],
    'permission' => [
        'tab' => 'Putra KJU Express',
        'access_users' => 'Manage users',
        'access_branches' => 'Manage branches',
        'access_services' => 'Manage services',
        'access_delivery_routes' => 'Manage delivery routes',
        'access_delivery_costs' => 'Manage delivery costs',
        'access_delivery_orders' => 'Manage delivery orders',
        'access_regions' => 'Manage regions',
        'access_statuses' => 'Manage delivery order statuses',
        'access_pickups' => 'Manage pickups',
        'is_supervisor' => 'Supervisor',
        'is_courier' => 'Courier',
        'is_operator' => 'Operator',
        'access_payment_data_for_delivery_orders' => 'Access payment data for delivery orders',
        'access_discount_for_delivery_orders' => 'Access discount for delivery orders',
        'access_int_delivery_orders' => 'Manage int. delivery orders',
        'access_int_delivery_routes' => 'Manage int. delivery routes',
        'access_int_statuses' => 'Manage int. delivery order statuses',
        'access_vendors' => 'Manage vendors',
        'is_checker' => 'Checker',
        'is_tracker' => 'Tracker',
        'access_banks' => 'Manage banks',
        'access_balances' => 'Manage balances',
        'access_transactions' => 'Manage transactions',
        'access_master_balances' => 'Manage master balances',
        'access_master_transactions' => 'Manage master transactions',

        'access_master_delivery_orders' => 'Manage master delivery orders',
        'access_master_int_delivery_orders' => 'Manage master int. delivery orders',
        'access_view_delivery_orders' => 'Access view delivery orders',
        'access_view_int_delivery_orders' => 'Access view international delivery orders',
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
        'service_not_available' => 'Service Not Available',
        'additional_cost' => 'Additional Cost',
        'region' => 'Region',
        'weight' => 'Weight (KG)',
        'amount' => 'Amount (Units)',
        'check_delivery_cost' => 'Check Delivery Cost',
        'master_data' => 'Master Data',
        'country' => 'Country',
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
        'agreement_for_total_cost' => 'Agreement for total cost',
        'agreement_for_discount' => 'Agreement for discount',
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
        'action_not_allowed' => 'Action not allowed',
        'insufficient_balance' => 'Insufficient balance',
        'print' => 'Print',
        'print_without_price' => 'Print Without Price',
        'process_date' => 'Process Date',
        'shipper' => 'Shipper',
        'consignee' => 'Consignee',
        'pickup_date' => 'Pickup Date',
        'order_code_not_found' => 'Order code not found.',
        'delivery_order_already_finish' => 'Delivery order already finish.',
        'delivery_order_still_pickup' => 'Delivery order still pickup.',
        'region_id_not_found' => 'Region id not found.',
        'status_not_allowed' => 'Status not allowed.',
        'origin_id' => 'Origin Id',
        'destination_id' => 'Destination Id',
        'report' => 'Report',
        'type' => 'Type',
        'outgoing' => 'Outgoing',
        'incoming' => 'Incoming',
        'discount' => 'Discount',
        'payment_status' => 'Payment Status',
        'payment_description' => 'Payment Description',
        'paid' => 'Paid',
        'unpaid' => 'Unpaid',
        'percentage' => 'Percentage (%)',
        'min_lead_time' => 'Lead Time (Min)',
        'max_lead_time' => 'Lead Time (Max)',
        'payment_data' => 'Payment Data',
        'shipper_data' => 'Shipper Data',
        'consignee_data' => 'Consignee Data',
        'goods_data' => 'Goods Data',
        'order_code' => 'Order Code',
        'pickup_courier' => 'Pickup Courier',
        'pickup_region' => 'Pickup Region',
        'pickup_request' => 'Pickup Request',
        'pickup_data' => 'Pickup Data',
        'pickup_address' => 'Pickup Address',
        'pickup_postal_code' => 'Pickup Postal Code',
        'consignee_address' => 'Consignee Address',
        'consignee_postal_code' => 'Consignee Postal Code',
        'consignee_phone_number' => 'Consignee Phone Number',
        'branch_region' => 'Branch Region',
        'consignee_region' => 'Consignee Region',
        'consignee_name' => 'Consignee Name',
        'goods_description' => 'Goods Description',
        'goods_amount' => 'Goods Amount',
        'goods_weight' => 'Goods Weight',
        'courier' => 'Courier',
        'received_date' => 'Received Date',
        'domestic' => 'Domestic',
        'international' => 'International',
        'goods_height' => 'Height (cm)',
        'goods_width' => 'Width (cm)',
        'goods_length' => 'Length (cm)',
        'branch_fee' => 'Branch Fee',
        'cash' => 'Cash',
        'transfer' => 'Transfer',
        'payment_method' => 'Payment Method',
        'profit_share' => 'Profit Share',
        'fee_percentage' => 'Fee Percentage',
        'import_status' => 'Import Status',
        'goods_volume_data' => 'Goods Volume Data',
        'master_route_data' => 'Master Route Data',
        'reject' => 'Reject',
        'export' => 'Export',
        'pending' => 'Pending',
        'action' => 'Action',
        'branch_total_cost' => 'Branch Total Cost',
        'checker_total_cost' => 'Checker Total Cost',
        'different_total_cost' => 'Different Total Cost',
        'checker_data' => 'Checker Data',
        'tracker_data' => 'Tracker Data',
        'comment' => 'Comment',
        'dom_fee_percentage' => 'Domestic Fee Percentage',
        'int_fee_percentage' => 'International Fee Percentage',
        'net_profit' => 'Net Profit',
        'goods_volume_weight' => 'Goods Volume Weight',
        'net_total_cost' => 'Net Total Cost',
        'tracking_number' => 'Tracking Number',
    ],
    'branch' => [
        'singular' => 'Branch',
        'plural' => 'Branches',
        'name' => 'Branch Name',
        'code' => 'Branch Code',
        'new' => 'New Branch',
        'edit' => 'Edit Branch',
        'preview' => 'Preview Branch',
    ],
    'service' => [
        'plural' => 'Services',
        'singular' => 'Service',
        'code' => 'Service Code',
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
    ],
    'delivery_cost' => [
        'plural' => 'Delivery Costs',
        'singular' => 'Delivery Cost',
        'comment_cost' => 'Basic cost for limited weight',
        'comment_add_cost' => 'Additional cost after exceeding the limit',
        'check' => 'Check Delivery Cost',
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
        'new' => 'New Delivery Order',
        'edit' => 'Edit Delivery Order',
        'preview' => 'Preview Delivery Order',
        'export' => 'Export Delivery Orders',
        'export_manifest' => 'Export Manifest Delivery Orders',
    ],
    'status' => [
        'plural' => 'Statuses',
        'singular' => 'Status',
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
    'delivery_order_status' => [
        'plural' => 'Delivery Order Statuses',
        'singular' => 'Delivery Order Status',
        'import' => 'Import Delivery Order Status',
    ],
    'pickup_order' => [
        'export' => 'Export Pickup Orders',
    ],
    'pickup' => [
        'plural' => 'Pickup List',
        'singular' => 'Pickup',
        'edit' => 'Edit Pickup',
    ],
    'int_delivery_order' => [
        'plural' => 'Int. Delivery Orders',
        'singular' => 'Int. Delivery Order',
        'new' => 'New Int. Delivery Order',
        'edit' => 'Edit Int. Delivery Order',
        'preview' => 'Preview Int. Delivery Order',
        'export' => 'Export Int. Delivery Orders',
        'export_manifest' => 'Export Int. Manifest Delivery Orders',
    ],
    'int_delivery_cost' => [
        'check' => 'Int. Check Delivery Cost',
        'range_weight' => 'Range Weight',
        'min_range_weight' => 'Min Range Weight',
        'max_range_weight' => 'Max Range Weight',
        'base_cost_per_kg' => 'Base Cost per KG',
        'profit_percentage' => 'Profit Percentage',
        'add' => 'Additional Costs',
    ],
    'int_delivery_order_status' => [
        'plural' => 'Int. Delivery Order Statuses',
        'singular' => 'Int. Delivery Order Status',
        'import' => 'Import Int. Delivery Order Status',
    ],
    'int_delivery_route' => [
        'plural' => 'Int. Delivery Routes',
        'singular' => 'Int. Delivery Route',
        'new' => 'New Int. Delivery Route',
        'edit' => 'Edit Int. Delivery Route',
        'preview' => 'Preview Int. Delivery Route',
    ],
    'goods_type' => [
        'singular' => 'Goods Type',
        'add_cost_per_kg' => 'Additional Cost Per KG',
    ],
    'vendor' => [
        'id' => 'Vendor Id',
        'name' => 'Vendor Name',
        'plural' => 'Vendors',
        'singular' => 'Vendor',
        'slug' => 'Vendor Slug',
        'new' => 'New Vendor',
        'edit' => 'Edit Vendor',
        'preview' => 'Preview Vendor',
    ],
    'balance' => [
        'singular' => 'Balance',
        'plural' => 'Balances',
        'id' => 'Balance Id',
    ],
    'transaction' => [
        'plural' => 'Transactions',
        'singular' => 'Transaction',
        'description' => 'Description',
        'amount' => 'Amount',
        'id' => 'Transaction Id',
        'last_balance' => 'Last Balance',
        'current_balance' => 'Current Balance'
    ],
    'owner' => [
        'name' => 'Owner Name',
        'type' => 'Owner Type',
        'singular' => 'Owner'
    ]
];