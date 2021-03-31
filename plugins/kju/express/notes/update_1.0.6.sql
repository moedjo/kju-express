ALTER TABLE kju_express_branches 
    ADD balance_id int(10) unsigned DEFAULT NULL;
ALTER TABLE kju_express_branches
     ADD CONSTRAINT `kju_express_branches_balance_id_foreign` FOREIGN KEY (`balance_id`) REFERENCES `kju_express_balances` (`id`)

ALTER TABLE backend_users 
    ADD balance_id int(10) unsigned DEFAULT NULL;
ALTER TABLE backend_users
     ADD CONSTRAINT `backend_users_balance_id_foreign` FOREIGN KEY (`balance_id`) REFERENCES `kju_express_balances` (`id`)

