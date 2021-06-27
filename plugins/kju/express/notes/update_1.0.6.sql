ALTER TABLE kju_express_delivery_orders ADD balance_id int(10) unsigned DEFAULT NULL NULL;

ALTER TABLE kju_express_delivery_orders 
ADD CONSTRAINT kju_express_delivery_orders_balance_id_foreign 
FOREIGN KEY (balance_id) 
REFERENCES kju_express_balances(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
