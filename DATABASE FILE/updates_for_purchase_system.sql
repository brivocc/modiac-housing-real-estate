-- Database updates for purchase system and access control
-- Add purchase functionality and role-based access

-- Create purchases table to track property purchases
CREATE TABLE IF NOT EXISTS `purchases` (
    `purchase_id` int(11) NOT NULL AUTO_INCREMENT,
    `property_id` int(11) NOT NULL,
    `buyer_id` int(11) NOT NULL,
    `seller_id` int(11) NOT NULL,
    `purchase_price` decimal(15,2) NOT NULL,
    `purchase_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payment_status` enum('pending','completed','cancelled') DEFAULT 'pending',
    `notes` text,
    PRIMARY KEY (`purchase_id`),
    KEY `property_id` (`property_id`),
    KEY `buyer_id` (`buyer_id`),
    KEY `seller_id` (`seller_id`),
    CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property` (`pid`) ON DELETE CASCADE,
    CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`uid`) ON DELETE CASCADE,
    CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `user` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add purchase_status column to property table
ALTER TABLE `property` ADD COLUMN `purchase_status` enum('available','sold') DEFAULT 'available' AFTER `status`;

-- Add purchase_price column to property table
ALTER TABLE `property` ADD COLUMN `purchase_price` decimal(15,2) DEFAULT NULL AFTER `price`;

-- Create a view for available properties
CREATE OR REPLACE VIEW available_properties AS 
SELECT * FROM property WHERE purchase_status = 'available' AND status = 'available';

-- Insert sample data for testing
INSERT INTO `purchases` (`property_id`, `buyer_id`, `seller_id`, `purchase_price`, `purchase_date`, `payment_status`) VALUES
(25, 28, 30, 219690.00, '2024-01-15 10:30:00', 'completed');

-- Update property 25 as sold for testing
UPDATE `property` SET `purchase_status` = 'sold' WHERE `pid` = 25;
