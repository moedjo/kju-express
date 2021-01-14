/* UPDATE COST & ADD_COST */
UPDATE kju_express_delivery_costs
SET 
	cost = cost + (cost * 10 / 100),
	add_cost = add_cost + (add_cost * 10 / 100)
WHERE service_code IN 
	('KJR','KJT10','KJT100','KJT30','KJT50')
AND delivery_route_code NOT IN 
	('3674-3273','3671-3273','3603-3273','3273-3674','3273-3671','3273-3603')