ALTER TABLE `kju_express_regions`
	CHANGE COLUMN `type` `type` 
    ENUM('country','province','regency','district') NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;
ALTER TABLE `kju_express_delivery_orders`
	CHANGE COLUMN `goods_weight` `goods_weight` DOUBLE(5,2) NOT NULL DEFAULT 0 AFTER `goods_amount`;
    
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6009600, 'BRUNEI DARUSSALAM', 'country', NULL, '2021-01-19 14:03:27', '2021-01-19 14:03:27');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6010400, 'MYANMAR', 'country', NULL, '2021-01-19 14:05:55', '2021-01-19 14:05:55');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6011600, 'CAMBODIA', 'country', NULL, '2021-01-19 14:05:40', '2021-01-19 14:05:40');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6015600, 'CHINA', 'country', NULL, '2021-01-19 14:04:23', '2021-01-19 14:04:23');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6015800, 'TAIWAN', 'country', NULL, '2021-01-19 14:02:45', '2021-01-19 14:02:45');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6034400, 'HONG KONG', 'country', NULL, '2021-01-19 14:03:00', '2021-01-19 14:03:00');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6039200, 'JAPAN', 'country', NULL, '2021-01-19 14:05:09', '2021-01-19 14:05:09');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6041000, 'SOUTH KOREA', 'country', NULL, '2021-01-19 14:04:55', '2021-01-19 14:04:55');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6044600, 'MACAU', 'country', NULL, '2021-01-19 14:04:10', '2021-01-19 14:04:10');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6045800, 'MALAYSIA', 'country', NULL, '2021-01-19 14:01:36', '2021-01-19 14:01:36');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6045812, 'SABAH', 'country', NULL, '2021-01-19 14:03:44', '2021-01-19 14:03:44');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6045813, 'SERAWAK', 'country', NULL, '2021-01-19 14:03:58', '2021-01-19 14:03:58');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6070200, 'SINGAPORE', 'country', NULL, '2021-01-19 14:01:20', '2021-01-19 14:01:20');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6070400, 'VIETNAM', 'country', NULL, '2021-01-19 14:05:22', '2021-01-19 14:05:22');
INSERT INTO `kju_express_regions` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES (6076400, 'THAILAND', 'country', NULL, '2021-01-19 14:04:39', '2021-01-19 14:04:39');

INSERT INTO `kju_express_goods_types` (`code`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES ('GG', 'General Goods', 0, NULL, NULL);
INSERT INTO `kju_express_goods_types` (`code`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES ('SC', 'Special Comudity', 0, NULL, NULL);
INSERT INTO `kju_express_goods_types` (`code`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES ('SF', 'Sensitive Food', 0, NULL, NULL);

INSERT INTO `kju_express_int_delivery_routes` (`code`, `created_at`, `updated_at`, `src_region_id`, `dst_region_id`) VALUES ('31-6070200', '2021-01-20 19:04:17', '2021-01-20 19:04:17', 31, 6070200);

INSERT INTO `kju_express_int_delivery_costs` (`min_range_weight`, `max_range_weight`, `base_cost_per_kg`, `profit_percentage`, `int_delivery_route_code`, `created_at`, `updated_at`) VALUES (1, 1, 55000, 45, '31-6070200', '2021-01-20 19:04:36', '2021-01-20 19:47:14');
INSERT INTO `kju_express_int_delivery_costs` (`min_range_weight`, `max_range_weight`, `base_cost_per_kg`, `profit_percentage`, `int_delivery_route_code`, `created_at`, `updated_at`) VALUES (2, 5, 45000, 73, '31-6070200', '2021-01-20 19:05:23', '2021-01-20 19:47:19');
INSERT INTO `kju_express_int_delivery_costs` (`min_range_weight`, `max_range_weight`, `base_cost_per_kg`, `profit_percentage`, `int_delivery_route_code`, `created_at`, `updated_at`) VALUES (6, 10, 44000, 77, '31-6070200', '2021-01-20 19:06:14', '2021-01-20 19:47:23');
INSERT INTO `kju_express_int_delivery_costs` (`min_range_weight`, `max_range_weight`, `base_cost_per_kg`, `profit_percentage`, `int_delivery_route_code`, `created_at`, `updated_at`) VALUES (11, 15, 43000, 81, '31-6070200', '2021-01-20 19:48:23', '2021-01-20 19:48:23');
INSERT INTO `kju_express_int_delivery_costs` (`min_range_weight`, `max_range_weight`, `base_cost_per_kg`, `profit_percentage`, `int_delivery_route_code`, `created_at`, `updated_at`) VALUES (16, 17, 42000, 85, '31-6070200', '2021-01-20 20:21:10', '2021-01-20 20:21:10');

INSERT INTO `kju_express_int_add_delivery_costs` (`add_cost_per_kg`, `goods_type_code`, `int_delivery_route_code`, `created_at`, `updated_at`) VALUES (1000, 'SF', '31-6070200', '2021-01-21 11:25:21', '2021-01-21 11:25:21');

