ALTER TABLE subscription_plan
ADD COLUMN stripe_price_id VARCHAR(100) NULL AFTER price;
