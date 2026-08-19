SET SESSION sql_require_primary_key = 0;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 22 يونيو 2026 الساعة 19:30
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `e-website`
--

-- --------------------------------------------------------

--
-- بنية الجدول `contact_messages`
--

CREATE TABLE `contact_messages` (
    `id` int(11) NOT NULL,
    `first_name` varchar(50) NOT NULL,
    `last_name` varchar(50) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `email` varchar(100) NOT NULL,
    `message` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `reply` text DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `elogin`
--

CREATE TABLE `elogin` (
    `id` int(11) NOT NULL,
    `full_name` varchar(100) NOT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) DEFAULT NULL,
    `failed_attempts` int(11) DEFAULT 0,
    `lockout_until` datetime DEFAULT NULL,
    `role` varchar(20) NOT NULL DEFAULT 'user',
    `verification_code` int(11) DEFAULT NULL,
    `is_banned` tinyint(1) DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `elogin`
--

INSERT INTO
    `elogin` (
        `id`,
        `full_name`,
        `name`,
        `email`,
        `password`,
        `failed_attempts`,
        `lockout_until`,
        `role`,
        `verification_code`,
        `is_banned`
    )
VALUES (
        1,
        '',
        'مدير النظام',
        'test@test.com',
        '$2y$10$bNcfzghyMCWanbpYy10bQ.GUgnzcaIK4yAM.B18ZgFHWh9qfT6TUu',
        0,
        NULL,
        'user',
        NULL,
        0
    ),
    (
        7,
        '',
        'ِAmr',
        'A@gmail.com',
        '$2y$10$5270brc0T.A91j3oqyT0euGtDdcm7MQ2u35qT76MnpprBgs5ajk6y',
        0,
        NULL,
        'admin',
        NULL,
        0
    );

-- --------------------------------------------------------

--
-- بنية الجدول `messages`
--

CREATE TABLE `messages` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `subject` varchar(255) DEFAULT 'استفسار عام',
    `message` text NOT NULL,
    `reply` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `messages`
--

INSERT INTO
    `messages` (
        `id`,
        `user_id`,
        `subject`,
        `message`,
        `reply`,
        `created_at`
    )
VALUES (
        1,
        1,
        'بببب بببب',
        'انا عندي مشكله',
        'نعتذر عن أي إزعاج. اشرح المشكلة بالتفصيل وسنعمل على حلها بأسرع وقت.',
        '2026-05-22 14:03:03'
    );

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
    `id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `full_name` varchar(100) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `address_line1` text NOT NULL,
    `address_line2` text DEFAULT NULL,
    `city` varchar(50) NOT NULL,
    `governorate` varchar(50) NOT NULL,
    `zip_code` varchar(10) DEFAULT NULL,
    `total_price` decimal(10, 2) NOT NULL,
    `status` enum(
        'قيد المراجعة',
        'تم الشحن',
        'مكتمل',
        'ملغي'
    ) NOT NULL DEFAULT 'قيد المراجعة',
    `user_hidden` tinyint(1) NOT NULL DEFAULT 0,
    `products` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `orders`
--

INSERT INTO
    `orders` (
        `id`,
        `user_id`,
        `full_name`,
        `phone`,
        `address_line1`,
        `address_line2`,
        `city`,
        `governorate`,
        `zip_code`,
        `total_price`,
        `status`,
        `user_hidden`,
        `products`,
        `created_at`
    )
VALUES (
        21,
        1,
        't555t@test.com',
        '50155555020000',
        'b',
        'b',
        'b',
        'b',
        'b',
        160.00,
        'تم الشحن',
        0,
        '[{\"id\":1782146924893,\"src\":\"http://localhost:8080/e-co/images/products/product-66.png\",\"title\":\"ماوس بلوتوث لاسلكي W10\",\"price\":\"160.00 جنيه\",\"completed\":true}]',
        '2026-06-22 16:49:13'
    );

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
    `id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `price` decimal(10, 2) NOT NULL,
    `category_class` varchar(50) NOT NULL,
    `image_url` varchar(255) NOT NULL,
    `rating` decimal(3, 1) NOT NULL DEFAULT 5.0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO
    `products` (
        `id`,
        `title`,
        `description`,
        `price`,
        `category_class`,
        `image_url`,
        `rating`
    )
VALUES (
        2,
        'هاتف ايفون 17 الجديد',
        NULL,
        84000.00,
        'هواتف',
        'images/products/product-1.png',
        5.0
    ),
    (
        3,
        'ساعة سامسونج جالكسي الترا',
        NULL,
        3460.00,
        'ساعات ذكية',
        'images/products/product-13.png',
        5.0
    ),
    (
        4,
        'موبايل ايفون 15 بلس سعة (128 جيجابايت)',
        NULL,
        44000.00,
        'هواتف',
        'images/products/product-3.png',
        5.0
    ),
    (
        5,
        'لابتوب من ديل، بمعالج انتل (مجدد)',
        NULL,
        16900.00,
        'لابتوب',
        'images/products/product-4.png',
        5.0
    ),
    (
        6,
        'لابتوب من ديل، بمعالج انتل، شاشة 15.6 إنش',
        NULL,
        22500.00,
        'لابتوب',
        'images/products/product-5.png',
        5.0
    ),
    (
        7,
        'موبايل شاومي ريدمي نوت 14 ذاكرة 8+256 جيجابايت',
        NULL,
        13800.00,
        'هواتف',
        'images/products/product-6.png',
        5.0
    ),
    (
        8,
        'موبايل شاومي ريدمي نوت 14 ذاكرة 8+256 جيجابايت 5G',
        NULL,
        9780.00,
        'هواتف',
        'images/products/product-30.png',
        5.0
    ),
    (
        9,
        'فلاش ميموري كلاسيك 2.0 بسعة 64 جيجابايت',
        NULL,
        290.00,
        'فلاشات',
        'images/products/product-7.png',
        5.0
    ),
    (
        11,
        'ساعة سامسونج جالكسي فيت 3 الذكية',
        NULL,
        2850.00,
        'ساعات ذكية',
        'images/products/product-9.png',
        5.0
    ),
    (
        12,
        'تابلت سامسونج جالكسي تاب S10 FE',
        NULL,
        24900.00,
        'جهاز لوحي',
        'images/products/product-10.png',
        5.0
    ),
    (
        13,
        'سانديسك ذاكرة فلاش الترا دوال درايف',
        NULL,
        580.00,
        'فلاشات',
        'images/products/product-11.png',
        5.0
    ),
    (
        14,
        'تابلت شاومي ريدمي باد اس اي 8.7',
        NULL,
        8500.00,
        'جهاز لوحي',
        'images/products/product-12.png',
        5.0
    ),
    (
        15,
        'موبايل سامسونج جالاكسي A56',
        NULL,
        19800.00,
        'هواتف',
        'images/products/product-2.png',
        5.0
    ),
    (
        16,
        'ساعة هواوي الذكية شاشة AMOLED',
        NULL,
        6400.00,
        'ساعات ذكية',
        'images/products/product-14.png',
        5.0
    ),
    (
        17,
        'فلاش اكسوديا USB 3.20 سعة 64 جيجابايت',
        NULL,
        320.00,
        'فلاشات',
        'images/products/product-15.png',
        5.0
    ),
    (
        18,
        'موسع نطاق واي فاي شبكي RE315',
        NULL,
        1590.00,
        'راوترات',
        'images/products/product-75.png',
        5.0
    ),
    (
        19,
        'راوتر تي بي لينك ارشر سي 54 AC1200',
        NULL,
        1200.00,
        'راوترات',
        'images/products/product-76.png',
        5.0
    ),
    (
        20,
        'محول واي فاي USB ارتشر T4U بلس',
        NULL,
        1300.00,
        'راوترات',
        'images/products/product-77.png',
        5.0
    ),
    (
        21,
        'راوتر تي بي لينك TL-WA3001 ثنائي النطاق',
        NULL,
        3733.77,
        'راوترات',
        'images/products/product-78.png',
        5.0
    ),
    (
        22,
        'آيفون 13 برو ماكس (مستعمل - حالة كسر زيرو)',
        NULL,
        32000.00,
        'مستعمل',
        'images/products/product-79.png',
        5.0
    ),
    (
        23,
        'لابتوب ماك بوك برو M1 (مستعمل)',
        NULL,
        45000.00,
        'مستعمل',
        'images/products/product-80.png',
        5.0
    ),
    (
        24,
        'سامسونج جالكسي S22 الترا (مستعمل)',
        NULL,
        24500.00,
        'مستعمل',
        'images/products/product-81.png',
        5.0
    ),
    (
        25,
        'آيباد إير الجيل الرابع (مستعمل)',
        NULL,
        18900.00,
        'مستعمل',
        'images/products/product-82.png',
        5.0
    ),
    (
        26,
        'ديل لاتيتيود 5420 معالج i5 (مستعمل)',
        NULL,
        15800.00,
        'مستعمل',
        'images/products/product-83.png',
        5.0
    ),
    (
        27,
        'إتش بي إيليت بوك 840 G5 (مستعمل)',
        NULL,
        12500.00,
        'مستعمل',
        'images/products/product-84.png',
        5.0
    ),
    (
        28,
        'بلايستيشن 4 برو 1 تيرا مع دراعين (مستعمل)',
        NULL,
        14000.00,
        'مستعمل',
        'images/products/product-85.png',
        5.0
    ),
    (
        29,
        'كاميرا كانون EOS 80D مع عدسة (مستعملة)',
        NULL,
        28000.00,
        'مستعمل',
        'images/products/product-86.png',
        5.0
    ),
    (
        30,
        'ساعة آبل الإصدار السادس 44 ملم (مستعملة)',
        NULL,
        9500.00,
        'مستعمل',
        'images/products/product-87.png',
        5.0
    ),
    (
        31,
        'لينوفو ثينك باد X1 كربون (مستعمل)',
        NULL,
        19800.00,
        'مستعمل',
        'images/products/product-88.png',
        5.0
    ),
    (
        32,
        'نينتندو سويتش النسخة المطورة (مستعمل)',
        NULL,
        11000.00,
        'مستعمل',
        'images/products/product-89.png',
        5.0
    ),
    (
        33,
        'فيت USB 3.1-32 جيجابايت من سانديسك',
        NULL,
        240.00,
        'فلاشات',
        'images/products/product-16.png',
        5.0
    ),
    (
        34,
        'تابلت سامسونج تاب A9 بلس',
        NULL,
        13900.00,
        'جهاز لوحي',
        'images/products/product-17.png',
        5.0
    ),
    (
        35,
        'باد X9a 8GB ذاكرة رام 128 السعة',
        NULL,
        11800.00,
        'جهاز لوحي',
        'images/products/product-18.png',
        5.0
    ),
    (
        36,
        'لابتوب من ديل، بمعالج انتل 16 جيجابايت',
        NULL,
        31500.00,
        'لابتوب',
        'images/products/product-19.png',
        5.0
    ),
    (
        37,
        'لابتوب من لينوفو، بمعالج AMD',
        NULL,
        15700.00,
        'لابتوب',
        'images/products/product-20.png',
        5.0
    ),
    (
        38,
        'كاميرا كانون EOS R50 مع عدسة 18-45 مم',
        NULL,
        38500.00,
        'كاميرات',
        'images/products/product-21.png',
        5.0
    ),
    (
        39,
        'كاميرا سوني Alpha a7 IV بدون مرآة',
        NULL,
        98000.00,
        'كاميرات',
        'images/products/product-22.png',
        5.0
    ),
    (
        40,
        'كاميرا نيكون D850 احترافية - دقة 45.7',
        NULL,
        115000.00,
        'كاميرات',
        'images/products/product-23.png',
        5.0
    ),
    (
        41,
        'كاميرا جو برو هيرو 12 بلاك',
        NULL,
        22000.00,
        'كاميرات',
        'images/products/product-24.png',
        5.0
    ),
    (
        42,
        'موبايل ذكي انفينيكس هوت 60i',
        NULL,
        5605.49,
        'هواتف',
        'images/products/product-25.png',
        5.0
    ),
    (
        43,
        'موبايل ذكي نوفا 13 بذاكرة رام 12',
        NULL,
        25999.00,
        'هواتف',
        'images/products/product-26.png',
        5.0
    ),
    (
        44,
        'موبايل جوجل بيكسل 9 برو ذكي',
        NULL,
        55990.00,
        'هواتف',
        'images/products/product-27.png',
        5.0
    ),
    (
        45,
        'موبايل نوثينج (3a) جيل خامس',
        NULL,
        24999.00,
        'هواتف',
        'images/products/product-28.png',
        5.0
    ),
    (
        46,
        'موبايل اوبو رينو شبكة الجيل الخامس',
        NULL,
        15600.00,
        'هواتف',
        'images/products/product-29.png',
        5.0
    ),
    (
        47,
        'ابل تابلت ايباد برو ام 4 مقاس 13',
        NULL,
        61890.00,
        'جهاز لوحي',
        'images/products/product-31.png',
        5.0
    ),
    (
        48,
        'تابلت ميت باد 11.5 سعة تخزين 256',
        NULL,
        22950.00,
        'جهاز لوحي',
        'images/products/product-32.png',
        5.0
    ),
    (
        49,
        'تابلت هواوي ميت باد 12X 2025',
        NULL,
        33289.99,
        'جهاز لوحي',
        'images/products/product-33.png',
        5.0
    ),
    (
        50,
        'آبل - آيباد برو 12.9 بوصة',
        NULL,
        52000.00,
        'جهاز لوحي',
        'images/products/product-34.png',
        5.0
    ),
    (
        51,
        'لينوفو تاب ون MDT G85 رام 4 جيجا',
        NULL,
        4569.00,
        'جهاز لوحي',
        'images/products/product-35.png',
        5.0
    ),
    (
        52,
        'جهاز ماك بوك اير 2025 (15 بوصة)',
        NULL,
        61494.00,
        'لابتوب',
        'images/products/product-36.png',
        5.0
    ),
    (
        53,
        'لابتوب من لينوفو، بمعالج انتل كور',
        NULL,
        60999.00,
        'لابتوب',
        'images/products/product-37.png',
        5.0
    ),
    (
        54,
        'ايسر لابتوب اسباير 5 رفيع',
        NULL,
        53850.00,
        'لابتوب',
        'images/products/product-38.png',
        5.0
    ),
    (
        55,
        'لاب توب برستيج 13 ايه اي ايفو',
        NULL,
        50699.00,
        'لابتوب',
        'images/products/product-39.png',
        5.0
    ),
    (
        56,
        'لينوفو لابتوب ثينك باد E14 الجيل الخامس',
        NULL,
        68042.00,
        'لابتوب',
        'images/products/product-40.png',
        5.0
    ),
    (
        57,
        'لاب توب ثينك بوك 14 G2 ITL',
        NULL,
        66000.00,
        'لابتوب',
        'images/products/product-41.png',
        5.0
    ),
    (
        58,
        'ساعة ذكية هواوي جي تي 6 برو 46 ملم',
        NULL,
        13500.00,
        'ساعات ذكية',
        'images/products/product-42.png',
        5.0
    ),
    (
        59,
        'ساعة هواوي GT 6 ذكية 46 ملم',
        NULL,
        8777.77,
        'ساعات ذكية',
        'images/products/product-43.png',
        5.0
    ),
    (
        60,
        'ساعة ريدمي 5 لايت بلاك تتميز بشاشة اموليد',
        NULL,
        2156.73,
        'ساعات ذكية',
        'images/products/product-44.png',
        5.0
    ),
    (
        61,
        'ساعة ذكية X10 الترا 3 بشاشة منحنية',
        NULL,
        720.00,
        'ساعات ذكية',
        'images/products/product-45.png',
        5.0
    ),
    (
        62,
        'ساعة ذكية WS10 من ويسمي',
        NULL,
        4500.00,
        'ساعات ذكية',
        'images/products/product-46.png',
        5.0
    ),
    (
        63,
        'ميرف - سلسلة X10 10 - رياضية',
        NULL,
        699.00,
        'ساعات ذكية',
        'images/products/product-47.png',
        5.0
    ),
    (
        64,
        'فلاشة داتا ترافيلر بسعة 128 جيجابايت',
        NULL,
        999.00,
        'فلاشات',
        'images/products/product-48.png',
        5.0
    ),
    (
        65,
        'ذاكرة فلاش الترا فلير من سانديسك',
        NULL,
        450.00,
        'فلاشات',
        'images/products/product-49.png',
        5.0
    ),
    (
        66,
        'سانديسك ذاكرة فلاش الترا لوكس USB',
        NULL,
        390.00,
        'فلاشات',
        'images/products/product-50.png',
        5.0
    ),
    (
        67,
        'بطاقة ذاكرة ميكرو اس دي كانفاس',
        NULL,
        429.00,
        'فلاشات',
        'images/products/product-51.png',
        5.0
    ),
    (
        68,
        'ليكسار ذاكرة فلاش يو اس بي 64 جيجابايت',
        NULL,
        1299.00,
        'فلاشات',
        'images/products/product-52.png',
        5.0
    ),
    (
        69,
        'هيكل كاميرا اي او اس R6 مارك',
        NULL,
        109444.00,
        'كاميرات',
        'images/products/product-53.png',
        5.0
    ),
    (
        70,
        'فيلتروكس عدسة 40 ملم F2.5 f/2.5 AF',
        NULL,
        8925.00,
        'كاميرات',
        'images/products/product-54.png',
        5.0
    ),
    (
        71,
        'نيكون هيكل كاميرا Z 6II FX-Format',
        NULL,
        107000.00,
        'كاميرات',
        'images/products/product-55.png',
        5.0
    ),
    (
        72,
        'كاميرا X-T5 فضية مع عدسة XF16',
        NULL,
        104999.00,
        'كاميرات',
        'images/products/product-56.png',
        5.0
    ),
    (
        73,
        'كاميرا رقمية بدقة FHD 1080',
        NULL,
        2940.00,
        'كاميرات',
        'images/products/product-57.png',
        5.0
    ),
    (
        74,
        'كاميرا رقمية بالنظام الكهروبصري',
        NULL,
        39199.00,
        'كاميرات',
        'images/products/product-58.png',
        5.0
    ),
    (
        75,
        'حامل موبايل اكريليك شفاف للمكتب',
        NULL,
        899.10,
        'اكسسوارات',
        'images/products/product-59.png',
        5.0
    ),
    (
        76,
        'حامل لاب توب من اوجيب قابل للطي',
        NULL,
        196.13,
        'اكسسوارات',
        'images/products/product-60.png',
        5.0
    ),
    (
        77,
        'حامل شاشة LCD فردي قابل للتعديل',
        NULL,
        1798.98,
        'اكسسوارات',
        'images/products/product-61.png',
        5.0
    ),
    (
        78,
        'جراب موبايل متوافق مع سامسونج',
        NULL,
        440.55,
        'اكسسوارات',
        'images/products/product-62.png',
        5.0
    ),
    (
        79,
        'سلسلة موبايل ماجيك حبل قابل للإزالة',
        NULL,
        90.00,
        'اكسسوارات',
        'images/products/product-63.png',
        5.0
    ),
    (
        80,
        'شاشة حماية زجاجية كاملة 5 دي',
        NULL,
        55.00,
        'اكسسوارات',
        'images/products/product-64.png',
        5.0
    ),
    (
        81,
        'شاشة حماية لهواوي ميت باد 12X',
        NULL,
        200.00,
        'اكسسوارات',
        'images/products/product-65.png',
        5.0
    ),
    (
        82,
        'ماوس بلوتوث لاسلكي W10',
        NULL,
        160.00,
        'اكسسوارات',
        'images/products/product-66.png',
        5.0
    ),
    (
        83,
        'لوحة مفاتيح ألعاب ميكانيكية (K6)',
        NULL,
        317.00,
        'اكسسوارات',
        'images/products/product-67.png',
        5.0
    ),
    (
        84,
        'ذراع تحكم اكس بوكس 360',
        NULL,
        380.00,
        'اكسسوارات',
        'images/products/product-68.png',
        5.0
    ),
    (
        85,
        'مودم راوتر تي بي لينك AC1200',
        NULL,
        2945.00,
        'راوترات',
        'images/products/product-69.png',
        5.0
    ),
    (
        86,
        'راوتر جيجابت واي فاي 6 نطاق مزدوج',
        NULL,
        1800.00,
        'راوترات',
        'images/products/product-70.png',
        5.0
    ),
    (
        87,
        'راوتر MT110 أسود يدعم اتصال USB',
        NULL,
        3108.00,
        'راوترات',
        'images/products/product-71.png',
        5.0
    ),
    (
        88,
        'دي اس ال X1852E لينك 6 AX1800',
        NULL,
        3333.00,
        'راوترات',
        'images/products/product-72.png',
        5.0
    ),
    (
        89,
        'لينكسيس راوتر مودم واي فاي ذكي',
        NULL,
        24999.00,
        'راوترات',
        'images/products/product-73.png',
        5.0
    ),
    (
        90,
        'تي بي لينك معزز نطاق WIFI',
        NULL,
        1305.00,
        'راوترات',
        'uploads/1776294518_69e01a767cea6.png',
        5.0
    ),
    (
        103,
        'ساعة ذكية مستعملة بحالة رائعة - تجربة متكاملة بسعر لا يُفوت',
        'هل تبحث عن تجربة الساعة الذكية المتكاملة دون الحاجة لدفع ثمن جديد بالكامل؟ نقدم لك ساعة ذكية مستعملة بحالة رائعة تجمع بين الأداء الممتاز والقيمة الاستثنائية. هذه الساعة هي فرصتك لامتلاك قطعة تكنولوجية عملية وأنيقة بسعر لا يُفوت، مما يجعلها خيارًا مثاليًا للميزانية الذكية.\r\nلماذا تختار هذه الساعة الذكية المستعملة؟\r\nقيمة لا تُضاهى: استمتع بجميع مميزات الساعة الذكية بسعر جزء من تكلفتها الأصلية، مما يوفر لك المال دون التنازل عن الجودة.\r\nحالة ممتازة: تم فحص الساعة بعناية للتأكد من أنها تعمل بكفاءة عالية وبحالة خارجية جيدة جدًا، مع الحد الأدنى من علامات الاستخدام.\r\nتتبع شامل للصحة واللياقة: راقب نبضات قلبك، خطواتك اليومية، وحرق السعرات الحرارية، وتابع جودة نومك لتحسين نمط حياتك.\r\nإشعارات في متناول يدك: استقبل إشعارات المكالمات والرسائل والتطبيقات مباشرة على معصمك، وابقَ على اتصال دائمًا دون الحاجة لإخراج هاتفك.\r\nتصميم أنيق وعصري: ارتدي ساعة تكمل أناقتك وتناسب مختلف الأنشطة اليومية، سواء كنت في العمل أو تمارس الرياضة.\r\nصديقة للبيئة: اختيارك لمنتج مستعمل يساهم في تقليل النفايات الإلكترونية وحماية البيئة.\r\nلا تدع هذه الفرصة تفوتك! احصل على ساعتك الذكية اليوم واستمتع بمزيج فريد من التكنولوجيا المتقدمة والسعر المناسب. إنها الصفقة الأمثل لكل من يبحث عن الذكاء والأناقة والتوفير.',
        3200.00,
        'مستعمل',
        'uploads/1776430635_69e22e2b0c87b.png',
        5.0
    );

-- --------------------------------------------------------

--
-- بنية الجدول `product_comments`
--

CREATE TABLE `product_comments` (
    `id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL COMMENT 'معرف المنتج',
    `customer_name` varchar(100) NOT NULL COMMENT 'اسم العميل',
    `comment_text` text NOT NULL COMMENT 'التعليق',
    `admin_reply` text DEFAULT NULL COMMENT 'رد الإدارة',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `settings`
--

CREATE TABLE `settings` (
    `id` int(11) NOT NULL,
    `about_text` text NOT NULL,
    `phone1` varchar(20) NOT NULL,
    `phone2` varchar(20) NOT NULL,
    `email` varchar(100) NOT NULL,
    `address` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elogin`
--
ALTER TABLE `elogin` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_comments`
--
ALTER TABLE `product_comments` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings` ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 33;

--
-- AUTO_INCREMENT for table `elogin`
--
ALTER TABLE `elogin`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 113;

--
-- AUTO_INCREMENT for table `product_comments`
--
ALTER TABLE `product_comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;

CREATE TABLE IF NOT EXISTS `online_users` (
    `session_id` varchar(255) NOT NULL,
    `last_activity` int(11) NOT NULL,
    PRIMARY KEY (`session_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;