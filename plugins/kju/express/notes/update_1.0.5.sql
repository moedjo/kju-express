ALTER TABLE `kju_express_regions`
	CHANGE COLUMN `type` `type` 
    ENUM('country','province','regency','district') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;
ALTER TABLE `kju_express_delivery_orders`
	CHANGE COLUMN `goods_weight` `goods_weight` DOUBLE(7,2) NOT NULL DEFAULT 0 AFTER `goods_amount`;


ALTER TABLE kju_express_delivery_orders ADD fee bigint(20) unsigned DEFAULT 0 NULL;
ALTER TABLE kju_express_delivery_orders ADD fee_percentage double(5,2) DEFAULT 0.00 NULL;
ALTER TABLE kju_express_delivery_orders ADD branch_total_cost bigint(20) DEFAULT 0 NULL;

ALTER TABLE kju_express_branches ADD dom_fee_percentage DOUBLE(5,2) DEFAULT 0.00 NULL;
ALTER TABLE kju_express_branches ADD int_fee_percentage DOUBLE(5,2) DEFAULT 0.00 NULL;
ALTER TABLE kju_express_delivery_orders ADD net_total_cost bigint(20) NULL;


ALTER TABLE kju_express_delivery_orders MODIFY COLUMN goods_description VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;


ALTER TABLE kju_express_customers ADD user_id int(10) unsigned NULL;
ALTER TABLE kju_express_customers DROP KEY kju_express_customers_phone_number_branch_code_unique;

ALTER TABLE kju_express_customers ADD CONSTRAINT kju_express_customers_unique UNIQUE KEY (phone_number,branch_code,user_id);

