/* UPDATE COST & ADD_COST */
UPDATE kju_express_delivery_costs
SET 
	cost = cost + (cost * 10 / 100),
	add_cost = add_cost + (add_cost * 10 / 100)
WHERE service_code IN 
	('KJR','KJT10','KJT100','KJT30','KJT50')
AND delivery_route_code NOT IN 
	('3674-3273','3671-3273','3603-3273','3273-3674','3273-3671','3273-3603')


select service_code,cost ,add_cost ,min_lead_time ,max_lead_time ,r.src_region_id, src.name ,r.dst_region_id, dst.name 
from kju_express_delivery_costs c, kju_express_delivery_routes r, kju_express_regions src, kju_express_regions dst
where c.delivery_route_code = r.code 
and src.id  = r.src_region_id 
and dst.id  = r.dst_region_id 