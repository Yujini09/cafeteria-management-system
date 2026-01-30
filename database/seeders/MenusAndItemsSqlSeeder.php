<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusAndItemsSqlSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared(<<<'SQL'
START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE menus;
TRUNCATE menu_items;
SET FOREIGN_KEY_CHECKS = 1;

/* =========================
   1) BREAKFAST
   ========================= */
-- Standard ₱150/head
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Standard Menu 1', 'standard', '', 'breakfast', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Longanisa w/ Slice Tomato','food',NOW(),NOW()),
(@m,'Fried Egg Sunny Side Up','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Standard Menu 2', 'standard', '', 'breakfast', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Pork Embutido','food',NOW(),NOW()),
(@m,'Onion Omelet','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Standard Menu 3', 'standard', '', 'breakfast', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Luncheon Meat','food',NOW(),NOW()),
(@m,'Dilis w/ Chopped Tomato','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Standard Menu 4', 'standard', '', 'breakfast', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Pork Tapa w/ Tomato','food',NOW(),NOW()),
(@m,'Salted Egg','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

-- Special ₱170/head
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Special Menu 1', 'special', '', 'breakfast', 170.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Fruit in Season','dessert',NOW(),NOW()),
(@m,'Longanisa w/ Slice Tomato','food',NOW(),NOW()),
(@m,'Boneless Daing na Bangus','food',NOW(),NOW()),
(@m,'Mushroom Omelet','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Special Menu 2', 'special', '', 'breakfast', 170.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Fruit in Season','dessert',NOW(),NOW()),
(@m,'Pork Omelet w/ Catsup','food',NOW(),NOW()),
(@m,'Fried Eggplant','food',NOW(),NOW()),
(@m,'Toasted Bread w/ Jam&Butter','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Special Menu 3', 'special', '', 'breakfast', 170.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Fruit in Season','dessert',NOW(),NOW()),
(@m,'Chicken Embutido','food',NOW(),NOW()),
(@m,'Fried Sausage','food',NOW(),NOW()),
(@m,'Fried Egg Sunny Side Up','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Special Menu 4', 'special', '', 'breakfast', 170.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (menu_id, name, type, created_at, updated_at) VALUES
(@m,'Nilagang Saba','dessert',NOW(),NOW()),
(@m,'Pork Tapa w/ Tomato','food',NOW(),NOW()),
(@m,'Salted Egg','food',NOW(),NOW()),
(@m,'Daing Dilis','food',NOW(),NOW()),
(@m,'Rice','food',NOW(),NOW()),
(@m,'Tea/Coffee','drink',NOW(),NOW()),
(@m,'Bottled Water','drink',NOW(),NOW());

/* =========================
   2) A.M. SNACKS
   ========================= */
-- Standard ₱100/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Standard • Day 1', 'standard', '', 'am_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Ham & Cheese Sandwich','food',NOW(),NOW()),
(NULL,@m,'Buko w/ Gulaman','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Standard • Day 2', 'standard', '', 'am_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Pimiento Sandwich','food',NOW(),NOW()),
(NULL,@m,'Buko w/ Gulaman','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Standard • Day 3', 'standard', '', 'am_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Chicken Sandwich','food',NOW(),NOW()),
(NULL,@m,'P/A Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Standard • Day 4', 'standard', '', 'am_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Cheese Burger','food',NOW(),NOW()),
(NULL,@m,'Iced Tea w/ Tanglad','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

-- Special ₱150/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Special • Day 1', 'special', '', 'am_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Lomi','food',NOW(),NOW()),
(NULL,@m,'Puto Cheese','food',NOW(),NOW()),
(NULL,@m,'Orange Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Special • Day 2', 'special', '', 'am_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Bihon Guisado','food',NOW(),NOW()),
(NULL,@m,'Kutsinta w/ Latik','food',NOW(),NOW()),
(NULL,@m,'Buko Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Special • Day 3', 'special', '', 'am_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Spaghetti w/ Meat Balls','food',NOW(),NOW()),
(NULL,@m,'P/A Orange Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('A.M. Snacks • Special • Day 4', 'special', '', 'am_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Carbonara w/ Chicken Fillet','food',NOW(),NOW()),
(NULL,@m,'4 Season Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

/* =========================
   3) LUNCH
   ========================= */
-- Standard ₱300/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Standard • Day 1', 'standard', '', 'lunch', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Chickenn Soup','food',NOW(),NOW()),
(NULL,@m,'Pork Karekare w/ Binagoongan','food',NOW(),NOW()),
(NULL,@m,'Lumpia Frito','food',NOW(),NOW()),
(NULL,@m,'Bolabola w/ P/A Sauce','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Molded Gulaman','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Standard • Day 2', 'standard', '', 'lunch', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Crab & Corn Soup','food',NOW(),NOW()),
(NULL,@m,'Pork w/ Mushroom','food',NOW(),NOW()),
(NULL,@m,'Chinese Veg. w/ Quail Egg','food',NOW(),NOW()),
(NULL,@m,'Fish Fillet w/ Sweet Chilli Sauce','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit in Season','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Standard • Day 3', 'standard', '', 'lunch', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Onion Soup','food',NOW(),NOW()),
(NULL,@m,'Cordon Bleu w/Creamy Mushroom-Sauce','food',NOW(),NOW()),
(NULL,@m,'Pork Bistick','food',NOW(),NOW()),
(NULL,@m,'Toge Guisado','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit in Season','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Standard • Day 4', 'standard', '', 'lunch', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Corn Soup','food',NOW(),NOW()),
(NULL,@m,'Pork Sarciado','food',NOW(),NOW()),
(NULL,@m,'Gising-gising','food',NOW(),NOW()),
(NULL,@m,'Fish Bolabola','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit in Season','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

-- Special ₱350/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Special • Day 1', 'special', '', 'lunch', 350.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Egg Drop Soup','food',NOW(),NOW()),
(NULL,@m,'Pork Caldereta','food',NOW(),NOW()),
(NULL,@m,'Chinese Vegetables','food',NOW(),NOW()),
(NULL,@m,'Sweet and Sour Fish','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Leche Flan','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Special • Day 2', 'special', '', 'lunch', 350.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Crab & Corn Soup','food',NOW(),NOW()),
(NULL,@m,'Steamed Veg. w/Butter Garlic Sauce','food',NOW(),NOW()),
(NULL,@m,'Chicken Pork Adobo w/Coco Cream','food',NOW(),NOW()),
(NULL,@m,'Fish Escabeche','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit Salad','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Special • Day 3', 'special', '', 'lunch', 350.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Sinigang na Hipon','food',NOW(),NOW()),
(NULL,@m,'Fried Chicken','food',NOW(),NOW()),
(NULL,@m,'Gising-gising','food',NOW(),NOW()),
(NULL,@m,'Slice Fruits','dessert',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Lunch • Special • Day 4', 'special', '', 'lunch', 350.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Bolabola Fish w/ Misua','food',NOW(),NOW()),
(NULL,@m,'Pork Karekare','food',NOW(),NOW()),
(NULL,@m,'Lumpia Shanghai','food',NOW(),NOW()),
(NULL,@m,'Breaded Chicken w/ P/A Sauce','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit Cocktail','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

/* =========================
   4) P.M. SNACKS
   ========================= */
-- Standard ₱100/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Standard • Day 1', 'standard', '', 'pm_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,"Cheese Burger Sandwich",'food',NOW(),NOW()),
(NULL,@m,"Sago't Gulaman",'drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Standard • Day 2', 'standard', '', 'pm_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Chicken Sandwich','food',NOW(),NOW()),
(NULL,@m,'P/A Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Standard • Day 3', 'standard', '', 'pm_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Tuna Sandwich','food',NOW(),NOW()),
(NULL,@m,'Iced Tea','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Standard • Day 4', 'standard', '', 'pm_snacks', 100.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Cheese Pimiento Sandwich','food',NOW(),NOW()),
(NULL,@m,'Black Gulaman','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

-- Special ₱150/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Special • Day 1', 'special', '', 'pm_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Carbonara','food',NOW(),NOW()),
(NULL,@m,'Toasted Bread','food',NOW(),NOW()),
(NULL,@m,'4 Season Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Special • Day 2', 'special', '', 'pm_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Sotanghon Guisado','food',NOW(),NOW()),
(NULL,@m,'Maja','food',NOW(),NOW()),
(NULL,@m,'Iced Tea w/ Lemon','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Special • Day 3', 'special', '', 'pm_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Bihon Guisado','food',NOW(),NOW()),
(NULL,@m,'Kutsinta w/ Latik','food',NOW(),NOW()),
(NULL,@m,"Sago't Gulaman",'drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('P.M. Snacks • Special • Day 4', 'special', '', 'pm_snacks', 150.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Spaghetti','food',NOW(),NOW()),
(NULL,@m,'Garlic Bread','food',NOW(),NOW()),
(NULL,@m,'P/A Juice','drink',NOW(),NOW()),
(NULL,@m,'Tea/Coffee','drink',NOW(),NOW()),
(NULL,@m,'Distilled Water','drink',NOW(),NOW());

/* =========================
   5) DINNER
   ========================= */
-- Standard ₱300/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Standard • Day 1', 'standard', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Egg Drop Soup','food',NOW(),NOW()),
(NULL,@m,'Mushroom Cabbage w/ Pork Balls','food',NOW(),NOW()),
(NULL,@m,'Chicken Caldereta','food',NOW(),NOW()),
(NULL,@m,'Fried Tilapia','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Banana','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Standard • Day 2', 'standard', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Bolabola w/ Misua','food',NOW(),NOW()),
(NULL,@m,'Breaded Pork','food',NOW(),NOW()),
(NULL,@m,'Bean w/ Ham Strips','food',NOW(),NOW()),
(NULL,@m,'Fried Bangus','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit in Season','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Standard • Day 3', 'standard', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Batchoy w/ Meat','food',NOW(),NOW()),
(NULL,@m,'Pinakbet','food',NOW(),NOW()),
(NULL,@m,'Fried Hito','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Leche Flan','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Standard • Day 4', 'standard', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Chicken Tinola','food',NOW(),NOW()),
(NULL,@m,'Inihaw na Tilapia','food',NOW(),NOW()),
(NULL,@m,'Lagalaga Veg. w/ Buro','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit Salad','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

-- Special ₱300/head (Day 1..4)
INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Special • Day 1', 'special', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Chicken Swam','food',NOW(),NOW()),
(NULL,@m,'Broiled Fish w/ Mango Salad','food',NOW(),NOW()),
(NULL,@m,'Lechon Kawali w/ Sauce','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Lagalaga Veg. Delight w/ Buro','food',NOW(),NOW()),
(NULL,@m,'Banana','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Special • Day 2', 'special', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Sinampalukang Manok','food',NOW(),NOW()),
(NULL,@m,'Pork Sisig','food',NOW(),NOW()),
(NULL,@m,'Pinakbet','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Fruit in Season','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Special • Day 3', 'special', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Batchoy Soup','food',NOW(),NOW()),
(NULL,@m,'Fried Tilapia w/ Mango Sisig','food',NOW(),NOW()),
(NULL,@m,'Broiled Eggplant w/ Binagoongan','food',NOW(),NOW()),
(NULL,@m,'Chicken Barbeque','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Buko Pandan','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

INSERT INTO menus (name, type, description, meal_time, price, created_at, updated_at)
VALUES ('Dinner • Special • Day 4', 'special', '', 'dinner', 300.00, NOW(), NOW());
SET @m := LAST_INSERT_ID();
INSERT INTO menu_items (id,menu_id,name,type,created_at,updated_at) VALUES
(NULL,@m,'Tinolang Manok','food',NOW(),NOW()),
(NULL,@m,'Pork Asado Chinese Style','food',NOW(),NOW()),
(NULL,@m,'Relleno Bangus','food',NOW(),NOW()),
(NULL,@m,'Rice','food',NOW(),NOW()),
(NULL,@m,'Sweetened Banana','dessert',NOW(),NOW()),
(NULL,@m,'Bottled Water','drink',NOW(),NOW());

COMMIT;
SQL);
    }
}
