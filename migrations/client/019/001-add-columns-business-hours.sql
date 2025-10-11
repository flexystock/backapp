ALTER TABLE `business_hours`
  ADD COLUMN `start_time2` TIME DEFAULT NULL AFTER `end_time`,
  ADD COLUMN `end_time2` TIME DEFAULT NULL AFTER `start_time2`;