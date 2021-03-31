
/* BRANCH */
ALTER TABLE backend_users 
	DROP INDEX `backend_users_branch_foreign`,
    DROP FOREIGN KEY backend_users_branch_foreign;

ALTER TABLE kju_express_customers 
    DROP INDEX `kju_express_customers_branch_code_foreign`,
    DROP FOREIGN KEY kju_express_customers_branch_code_foreign;

ALTER TABLE kju_express_delivery_orders 
 	DROP INDEX `kju_express_delivery_orders_branch_code_foreign`,
    DROP FOREIGN KEY kju_express_delivery_orders_branch_code_foreign;


ALTER TABLE kju_express_branches 
    DROP PRIMARY KEY;

ALTER TABLE kju_express_branches 
    ADD id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST;

ALTER TABLE `backend_users`
	ADD COLUMN `branch_id` INT UNSIGNED NULL AFTER `branch_code`,
	ADD CONSTRAINT `backend_users_branch_foreign` FOREIGN KEY (`branch_id`) REFERENCES `kju_express_branches` (`id`);

UPDATE `backend_users` a
    INNER JOIN kju_express_branches b ON a.branch_code=b.code
    SET a.branch_id = b.id ;

ALTER TABLE `backend_users`
	DROP COLUMN `branch_code`;

ALTER TABLE `kju_express_customers`
	ADD COLUMN `branch_id` INT UNSIGNED NULL AFTER `branch_code`,
	ADD CONSTRAINT `kju_express_customers_branch_foreign` FOREIGN KEY (`branch_id`) REFERENCES `kju_express_branches` (`id`);

UPDATE `kju_express_customers` a
    INNER JOIN kju_express_branches b ON a.branch_code=b.code
    SET a.branch_id = b.id ;
ALTER TABLE `kju_express_customers`
	DROP INDEX `kju_express_customers_unique`,
	ADD UNIQUE INDEX `kju_express_customers_unique` (`phone_number`, `branch_id`, `user_id`);
ALTER TABLE `kju_express_customers`
	DROP COLUMN `branch_code`;

ALTER TABLE `kju_express_delivery_orders`
	ADD COLUMN `branch_id` INT UNSIGNED NULL AFTER `branch_code`,
	ADD CONSTRAINT `kju_express_delivery_orders_branch_foreign` FOREIGN KEY (`branch_id`) REFERENCES `kju_express_branches` (`id`);
UPDATE `kju_express_delivery_orders` a
    INNER JOIN kju_express_branches b ON a.branch_code=b.code
    SET a.branch_id = b.id ;
ALTER TABLE `kju_express_delivery_orders`
	DROP COLUMN `branch_code`;


/* SERVICE */
ALTER TABLE `kju_express_delivery_orders`
	DROP INDEX `kju_express_delivery_orders_service_code_foreign`,
	DROP FOREIGN KEY `kju_express_delivery_orders_service_code_foreign`;

ALTER TABLE `kju_express_delivery_costs`
	DROP INDEX `kju_express_delivery_costs_service_code_foreign`,
	DROP FOREIGN KEY `kju_express_delivery_costs_service_code_foreign`;

ALTER TABLE kju_express_services 
    DROP PRIMARY KEY;

ALTER TABLE kju_express_services 
    ADD id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST;

ALTER TABLE `kju_express_delivery_orders`
	ADD COLUMN `service_id` INT UNSIGNED NULL AFTER `service_code`,
	ADD CONSTRAINT `kju_express_delivery_orders_service_foreign` FOREIGN KEY (`service_id`) REFERENCES `kju_express_services` (`id`);

UPDATE `kju_express_delivery_orders` a
    INNER JOIN kju_express_services b ON a.service_code=b.code
    SET a.service_id = b.id ;

ALTER TABLE `kju_express_delivery_orders`
	DROP COLUMN `service_code`;

ALTER TABLE `kju_express_delivery_costs`
	ADD COLUMN `service_id` INT UNSIGNED NULL AFTER `service_code`,
	ADD CONSTRAINT `kju_express_delivery_costs_service_foreign` FOREIGN KEY (`service_id`) REFERENCES `kju_express_services` (`id`);

UPDATE `kju_express_delivery_costs` a
    INNER JOIN kju_express_services b ON a.service_code=b.code
    SET a.service_id = b.id ;



/* ROUTE */



ALTER TABLE `kju_express_delivery_costs`
	DROP FOREIGN KEY `kju_express_delivery_costs_delivery_route_code_foreign`;

ALTER TABLE `kju_express_delivery_costs`
	DROP INDEX `route_service_unique`;

ALTER TABLE kju_express_delivery_routes 
    DROP PRIMARY KEY;
ALTER TABLE kju_express_delivery_routes 
    ADD id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST;

ALTER TABLE `kju_express_delivery_costs`
	ADD COLUMN `delivery_route_id` INT UNSIGNED NULL AFTER `delivery_route_code`,
	ADD CONSTRAINT `kju_express_delivery_costs_delivery_route_foreign` 
    FOREIGN KEY (`delivery_route_id`) REFERENCES `kju_express_delivery_routes` (`id`);

UPDATE `kju_express_delivery_costs` a
    INNER JOIN kju_express_delivery_routes b ON a.delivery_route_code=b.code
    SET a.delivery_route_id = b.id ;

ALTER TABLE `kju_express_delivery_costs`
    ADD UNIQUE INDEX `kju_express_delivery_costs_unique` (`delivery_route_id`, `service_id`) USING BTREE;

ALTER TABLE `kju_express_delivery_costs`
	DROP COLUMN `delivery_route_code`;

ALTER TABLE `kju_express_delivery_costs`
	DROP COLUMN `service_code`;

/* ORDER */
ALTER TABLE `kju_express_delivery_order_statuses`
	DROP INDEX `kju_express_delivery_order_statuses_delivery_order_code_foreign`,
	DROP FOREIGN KEY `kju_express_delivery_order_statuses_delivery_order_code_foreign`;

ALTER TABLE kju_express_delivery_orders 
    DROP PRIMARY KEY;
ALTER TABLE kju_express_delivery_orders 
    ADD id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST;

ALTER TABLE `kju_express_delivery_order_statuses`
	ADD COLUMN `delivery_order_id` INT UNSIGNED NULL AFTER `delivery_order_code`,
	ADD CONSTRAINT `kju_express_delivery_order_statuses_delivery_order_foreign` 
    FOREIGN KEY (`delivery_order_id`) REFERENCES `kju_express_delivery_orders` (`id`);

UPDATE `kju_express_delivery_order_statuses` a
    INNER JOIN kju_express_delivery_orders b ON a.delivery_order_code=b.code
    SET a.delivery_order_id = b.id ;

ALTER TABLE `kju_express_delivery_order_statuses`
	DROP COLUMN `delivery_order_code`;

ALTER TABLE `kju_express_branches`
	ADD UNIQUE INDEX `kju_express_branches_code_unique` (`code`);
ALTER TABLE `kju_express_delivery_orders`
	ADD UNIQUE INDEX `kju_express_delivery_orders_code_unique` (`code`);

ALTER TABLE `kju_express_delivery_routes`
	ADD UNIQUE INDEX `kju_express_delivery_routes_code_unique` (`code`);


ALTER TABLE `kju_express_services`
	ADD UNIQUE INDEX `kju_express_kju_express_services_code_unique` (`code`);













