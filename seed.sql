-- ==========================================
-- 1. Admins
-- ==========================================
INSERT INTO `admins` (`name`, `email`, `password`, `national_id`, `avatar_image`, `created_at`, `updated_at`)
VALUES
('Super Admin', 'admin1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11111111111111', NULL, NOW(), NOW()),
('Manager 1', 'admin2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '22222222222222', NULL, NOW(), NOW()),
('Manager 2', 'admin3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '33333333333333', NULL, NOW(), NOW());

-- ==========================================
-- 2. Floors (linked to Admins)
-- ==========================================
INSERT INTO `floors` (`name`, `number`, `admin_id`, `created_at`, `updated_at`)
VALUES
('First Floor', 1, 1, NOW(), NOW()),
('Second Floor', 2, 1, NOW(), NOW()),
('Third Floor', 3, 2, NOW(), NOW()),
('Fourth Floor', 4, 2, NOW(), NOW()),
('Fifth Floor', 5, 3, NOW(), NOW());

-- ==========================================
-- 3. Rooms (linked to Floors & Admins)
-- ==========================================
INSERT INTO `rooms` (`number`, `capacity`, `price`, `floor_id`, `admin_id`, `created_at`, `updated_at`)
VALUES
(101, 2, 100.00, 1, 1,  NOW(), NOW()),
(102, 4, 150.00, 1, 1,  NOW(), NOW()),
(201, 2, 120.00, 2, 1,  NOW(), NOW()),
(202, 3, 130.00, 2, 1,  NOW(), NOW()),
(301, 1, 80.00, 3, 2,  NOW(), NOW()),
(302, 2, 110.00, 3, 2,  NOW(), NOW()),
(401, 4, 200.00, 4, 2, NOW(), NOW()),
(402, 3, 180.00, 4, 2,  NOW(), NOW()),
(501, 2, 120.00, 5, 3,  NOW(), NOW()),
(502, 5, 250.00, 5, 3,  NOW(), NOW());

-- ==========================================
-- 4. Clients
-- ==========================================
INSERT INTO `clients` (`name`, `email`, `password`, `country`, `gender`, `is_approved`, `approved_by`, `created_at`, `updated_at`)
VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'USA', 'male', 1, 1, NOW(), NOW()),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'UK', 'female', 0, NULL, NOW(), NOW()),
('Ali Hassan', 'ali@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Egypt', 'male', 1, 2, NOW(), NOW()),
('Sara Ahmed', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Egypt', 'female', 1, 3, NOW(), NOW()),
('Li Wei', 'li@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'China', 'other', 0, NULL, NOW(), NOW());

-- ==========================================
-- 5. Reservations
-- ==========================================
-- ملاحظات: room_id هنا = id الفعلي للغرف (حسب AUTO_INCREMENT)
-- Rooms أدخلناهم ترتيبهم من 1 إلى 10 => ids من 1 لـ 10
INSERT INTO `reservations` (`client_id`, `room_id`, `accompany_number`, `paid_price`,  `created_at`, `updated_at`)
VALUES
(1, 1, 1, 100.00,  NOW(), NOW()), -- John Doe يحجز الغرفة 101
(3, 4, 2, 260.00,  NOW(), NOW()), -- Ali Hassan يحجز الغرفة 202
(4, 7, 3, 600.00,  NOW(), NOW()), -- Sara Ahmed يحجز الغرفة 401
(1, 9, 1, 120.00,  NOW(), NOW()); -- John Doe يحجز الغرفة 501
