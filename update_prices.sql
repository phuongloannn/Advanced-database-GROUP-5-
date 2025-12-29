-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Cập nhật giá trong bảng products
UPDATE `products` SET 
    `original_price` = `original_price` * 10000,
    `selling_price` = `selling_price` * 10000;

-- Cập nhật giá trong bảng order_detail
UPDATE `order_detail` SET 
    `selling_price` = `selling_price` * 10000;

-- Cập nhật lại dữ liệu mẫu trong bảng products
UPDATE `products` SET
    `original_price` = 12470000,
    `selling_price` = 12000000
WHERE `id` = 45;

UPDATE `products` SET
    `original_price` = 30430000,
    `selling_price` = 30000000
WHERE `id` = 47;

UPDATE `products` SET
    `original_price` = 3000000,
    `selling_price` = 2750000
WHERE `id` = 48;

UPDATE `products` SET
    `original_price` = 2170000,
    `selling_price` = 2000000
WHERE `id` = 49;

UPDATE `products` SET
    `original_price` = 4340000,
    `selling_price` = 4000000
WHERE `id` = 51;

UPDATE `products` SET
    `original_price` = 1600000,
    `selling_price` = 1400000
WHERE `id` = 52;

UPDATE `products` SET
    `original_price` = 1560000,
    `selling_price` = 1200000
WHERE `id` = 53;

UPDATE `products` SET
    `original_price` = 1170000,
    `selling_price` = 1000000
WHERE `id` = 54;

UPDATE `products` SET
    `original_price` = 2390000,
    `selling_price` = 2200000
WHERE `id` = 55;

UPDATE `products` SET
    `original_price` = 2650000,
    `selling_price` = 2500000
WHERE `id` = 60;

UPDATE `products` SET
    `original_price` = 3000000,
    `selling_price` = 2500000
WHERE `id` = 61;

COMMIT; 