ALTER TABLE `kju_express_regions`
	CHANGE COLUMN `type` `type` 
    ENUM('country','province','regency','district') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;
ALTER TABLE `kju_express_delivery_orders`
	CHANGE COLUMN `goods_weight` `goods_weight` DOUBLE(5,2) NOT NULL DEFAULT 0 AFTER `goods_amount`;

ALTER TABLE `kju_express_branches`
	ADD COLUMN `fee_percentage` SMALLINT NULL DEFAULT '0' AFTER `name`;

ALTER TABLE kju_express_delivery_orders MODIFY COLUMN goods_description VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
