ALTER TABLE `kju_express_regions`
	CHANGE COLUMN `type` `type` 
    ENUM('country','province','regency','district') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;
ALTER TABLE `kju_express_delivery_orders`
	CHANGE COLUMN `goods_weight` `goods_weight` DOUBLE(5,2) NOT NULL DEFAULT 0 AFTER `goods_amount`;

ALTER TABLE `kju_express_branches`
	CHANGE COLUMN `fee_percentage` `fee_percentage` SMALLINT NULL DEFAULT '0' AFTER `name`;
