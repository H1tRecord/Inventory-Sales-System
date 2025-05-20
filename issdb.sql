-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 06:37 AM
-- Server version: 10.4.32-MariaDB
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
-- Database: `group2db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
    `Category_ID` int(11) NOT NULL,
    `Category_Name` varchar(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO
    `category` (
        `Category_ID`,
        `Category_Name`
    )
VALUES (7, 'Cases'),
    (8, 'Cooling'),
    (1, 'CPUs'),
    (5, 'Graphics Cards'),
    (2, 'Motherboards'),
    (6, 'Power Supplies'),
    (3, 'RAM'),
    (4, 'Storage Devices');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
    `Employee_ID` int(11) NOT NULL,
    `Name` varchar(100) NOT NULL,
    `Email` varchar(100) NOT NULL,
    `PhoneNo` varchar(15) DEFAULT NULL,
    `Job_ID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO
    `employee` (
        `Employee_ID`,
        `Name`,
        `Email`,
        `PhoneNo`,
        `Job_ID`
    )
VALUES (
        1,
        'Sam',
        'sam@gmail.com',
        '09923951478',
        1
    ),
    (
        2,
        'Kyle',
        'kyle@gmail.com',
        '09923951112',
        2
    );

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
    `Job_ID` int(11) NOT NULL,
    `Job_Title` varchar(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `job`
--

INSERT INTO
    `job` (`Job_ID`, `Job_Title`)
VALUES (1, 'Manager'),
    (2, 'Cashier');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
    `Product_ID` int(11) NOT NULL,
    `Product_Code` varchar(50) NOT NULL,
    `Product_Name` varchar(100) NOT NULL,
    `Description` text DEFAULT NULL,
    `Category_ID` int(11) DEFAULT NULL,
    `In_Stock` int(11) NOT NULL,
    `Selling_Price` decimal(10, 2) NOT NULL,
    `Product_Added` datetime NOT NULL,
    `Supplier_ID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO
    `product` (
        `Product_ID`,
        `Product_Code`,
        `Product_Name`,
        `Description`,
        `Category_ID`,
        `In_Stock`,
        `Selling_Price`,
        `Product_Added`,
        `Supplier_ID`
    )
VALUES (
        1,
        'CPU001',
        'AMD Ryzen 7 5800X',
        '8-core, 16-thread desktop processor',
        1,
        3,
        299.99,
        '2025-04-01 10:00:00',
        1
    ),
    (
        2,
        'MB001',
        'ASUS ROG B550-F',
        'ATX AM4 Gaming Motherboard',
        2,
        112,
        189.99,
        '2025-04-02 14:30:00',
        2
    ),
    (
        3,
        'RAM001',
        'Corsair Vengeance 32GB',
        'DDR4 3600MHz RGB RAM Kit',
        3,
        5,
        129.99,
        '2025-04-03 09:00:00',
        1
    ),
    (
        4,
        'SSD001',
        'Samsung 970 EVO Plus',
        '1TB NVMe M.2 SSD',
        4,
        102,
        99.99,
        '2025-04-04 11:00:00',
        2
    ),
    (
        5,
        'GPU001',
        'NVIDIA RTX 4070',
        '12GB GDDR6X Graphics Card',
        5,
        2,
        599.99,
        '2025-04-05 15:00:00',
        1
    ),
    (
        6,
        'PSU001',
        'Corsair RM850x',
        '850W 80+ Gold PSU',
        6,
        0,
        149.99,
        '2025-04-07 09:00:00',
        5
    ),
    (
        7,
        'CASE001',
        'Lian Li O11 Dynamic',
        'Mid-Tower ATX Case',
        7,
        1,
        159.99,
        '2025-04-07 10:30:00',
        6
    ),
    (
        8,
        'COOL001',
        'NZXT Kraken X53',
        '240mm AIO Liquid Cooler',
        8,
        0,
        129.99,
        '2025-04-07 11:45:00',
        7
    ),
    (
        9,
        'GPU002',
        'AMD RX 6800 XT',
        '16GB GDDR6 Graphics Card',
        5,
        20,
        549.99,
        '2025-04-07 13:15:00',
        5
    ),
    (
        10,
        'SSD002',
        'WD Black SN850X',
        '2TB NVMe SSD',
        4,
        102,
        179.99,
        '2025-04-07 14:30:00',
        7
    ),
    (
        17,
        'CPU002',
        'Intel Core i9-13900K',
        '24-core, 32-thread desktop processor',
        1,
        0,
        589.99,
        '2025-04-10 09:00:00',
        1
    ),
    (
        18,
        'MB002',
        'MSI MPG B650 EDGE',
        'ATX AM5 Gaming Motherboard',
        2,
        2,
        229.99,
        '2025-04-10 10:00:00',
        2
    ),
    (
        19,
        'RAM002',
        'G.Skill Trident Z5',
        'DDR5 6000MHz RGB 32GB Kit',
        3,
        1,
        189.99,
        '2025-04-10 11:00:00',
        5
    ),
    (
        20,
        'GPU003',
        'AMD RX 7900 XTX',
        '24GB GDDR6 Graphics Card',
        5,
        0,
        899.99,
        '2025-04-10 12:00:00',
        6
    ),
    (
        21,
        'COOL002',
        'Arctic Liquid Freezer II',
        '360mm AIO Liquid Cooler',
        8,
        3,
        149.99,
        '2025-04-10 13:00:00',
        7
    );

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
    `Supplier_ID` int(11) NOT NULL,
    `Supplier_Name` varchar(100) NOT NULL,
    `Contact_Number` varchar(15) DEFAULT NULL,
    `Email` varchar(100) DEFAULT NULL,
    `Address` varchar(255) NOT NULL DEFAULT ''
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO
    `supplier` (
        `Supplier_ID`,
        `Supplier_Name`,
        `Contact_Number`,
        `Email`,
        `Address`
    )
VALUES (
        1,
        'PC Components Direct',
        '09171234567',
        'sales@pccdirect.com',
        '123 Tech Street, Silicon Valley'
    ),
    (
        2,
        'GlobalTech Solutions',
        '09179876543',
        'orders@globaltech.com',
        '456 Digital Ave, Tech City'
    ),
    (
        5,
        'RAM & Storage Plus',
        '09181234567',
        'info@ramstorage.com',
        '789 Memory Lane, Cyber City'
    ),
    (
        6,
        'Gaming Hardware Pro',
        '09189876543',
        'sales@gaminghw.com',
        '321 GPU Street, Gaming District'
    ),
    (
        7,
        'Cooling Systems Inc',
        '09187654321',
        'support@coolingsys.com',
        '567 Thermal Road, Tech Park'
    );

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
    `Transaction_ID` int(11) NOT NULL,
    `Customer_Name` varchar(100) NOT NULL,
    `Customer_Email` varchar(100) DEFAULT NULL,
    `Customer_Phone` varchar(15) DEFAULT NULL,
    `No_of_Items_Bought` int(11) NOT NULL,
    `Transaction_Date` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO
    `transaction` (
        `Transaction_ID`,
        `Customer_Name`,
        `Customer_Email`,
        `Customer_Phone`,
        `No_of_Items_Bought`,
        `Transaction_Date`
    )
VALUES (
        1,
        'Robert Wilson',
        'robert.wilson@example.com',
        '09191234567',
        2,
        '2025-01-05 09:30:00'
    ),
    (
        2,
        'Emma Davis',
        'emma.davis@example.com',
        '09192345678',
        1,
        '2025-01-12 14:20:00'
    ),
    (
        3,
        'James Anderson',
        'james.anderson@example.com',
        '09193456789',
        2,
        '2025-01-25 16:45:00'
    ),
    (
        4,
        'Linda Taylor',
        'linda.taylor@example.com',
        '09194567890',
        3,
        '2025-02-03 10:15:00'
    ),
    (
        5,
        'William Thomas',
        'william.thomas@example.com',
        '09195678901',
        2,
        '2025-02-14 13:30:00'
    ),
    (
        6,
        'Olivia Moore',
        'olivia.moore@example.com',
        '09196789012',
        1,
        '2025-02-28 15:20:00'
    ),
    (
        7,
        'Richard Jackson',
        'richard.jackson@example.com',
        '09197890123',
        2,
        '2025-03-07 11:00:00'
    ),
    (
        8,
        'Sophia White',
        'sophia.white@example.com',
        '09198901234',
        1,
        '2025-03-15 14:45:00'
    ),
    (
        9,
        'Daniel Harris',
        'daniel.harris@example.com',
        '09199012345',
        3,
        '2025-03-28 16:30:00'
    ),
    (
        10,
        'Marcus Chen',
        'marcus.chen@example.com',
        '09199123456',
        3,
        '2025-04-08 10:15:00'
    ),
    (
        11,
        'Rachel Singh',
        'rachel.singh@example.com',
        '09199234567',
        2,
        '2025-04-15 14:30:00'
    ),
    (
        12,
        'Laura Kim',
        'laura.kim@example.com',
        '09199345678',
        1,
        '2025-04-22 16:45:00'
    ),
    (
        13,
        'Kevin Patel',
        'kevin.patel@example.com',
        '09199456789',
        2,
        '2025-05-05 09:30:00'
    ),
    (
        14,
        'Sofia Santos',
        'sofia.santos@example.com',
        '09199567890',
        3,
        '2025-05-12 13:45:00'
    );

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
    `ID` int(11) NOT NULL,
    `Transaction_ID` int(11) NOT NULL,
    `Product_Code` varchar(50) NOT NULL,
    `Product_Name` varchar(100) NOT NULL,
    `Quantity` int(11) NOT NULL,
    `Price` decimal(10, 2) NOT NULL,
    `Employee_Name` varchar(100) NOT NULL,
    `Job_Title` varchar(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO
    `transaction_details` (
        `ID`,
        `Transaction_ID`,
        `Product_Code`,
        `Product_Name`,
        `Quantity`,
        `Price`,
        `Employee_Name`,
        `Job_Title`
    )
VALUES (
        1,
        1,
        'CPU001',
        'AMD Ryzen 7 5800X',
        1,
        299.99,
        'Alice Smith',
        'Manager'
    ),
    (
        2,
        1,
        'RAM001',
        'Corsair Vengeance 32GB',
        1,
        129.99,
        'Alice Smith',
        'Manager'
    ),
    (
        3,
        2,
        'GPU001',
        'NVIDIA RTX 4070',
        1,
        599.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        4,
        3,
        'MB001',
        'ASUS ROG B550-F',
        1,
        189.99,
        'Alice Smith',
        'Manager'
    ),
    (
        5,
        3,
        'SSD001',
        'Samsung 970 EVO Plus',
        1,
        99.99,
        'Alice Smith',
        'Manager'
    ),
    (
        6,
        4,
        'PSU001',
        'Corsair RM850x',
        1,
        149.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        7,
        4,
        'CASE001',
        'Lian Li O11 Dynamic',
        1,
        159.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        8,
        4,
        'COOL001',
        'NZXT Kraken X53',
        1,
        129.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        9,
        5,
        'GPU002',
        'AMD RX 6800 XT',
        1,
        549.99,
        'Alice Smith',
        'Manager'
    ),
    (
        10,
        5,
        'SSD002',
        'WD Black SN850X',
        1,
        179.99,
        'Alice Smith',
        'Manager'
    ),
    (
        11,
        6,
        'CPU001',
        'AMD Ryzen 7 5800X',
        1,
        299.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        12,
        7,
        'RAM001',
        'Corsair Vengeance 32GB',
        2,
        129.99,
        'Alice Smith',
        'Manager'
    ),
    (
        13,
        7,
        'SSD001',
        'Samsung 970 EVO Plus',
        1,
        99.99,
        'Alice Smith',
        'Manager'
    ),
    (
        14,
        8,
        'GPU001',
        'NVIDIA RTX 4070',
        1,
        599.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        15,
        9,
        'PSU001',
        'Corsair RM850x',
        1,
        149.99,
        'Alice Smith',
        'Manager'
    ),
    (
        16,
        9,
        'CASE001',
        'Lian Li O11 Dynamic',
        1,
        159.99,
        'Alice Smith',
        'Manager'
    ),
    (
        17,
        9,
        'COOL001',
        'NZXT Kraken X53',
        1,
        129.99,
        'Alice Smith',
        'Manager'
    ),
    (
        18,
        10,
        'CPU001',
        'AMD Ryzen 7 5800X',
        1,
        299.99,
        'Alice Smith',
        'Manager'
    ),
    (
        19,
        10,
        'RAM001',
        'Corsair Vengeance 32GB',
        2,
        129.99,
        'Alice Smith',
        'Manager'
    ),
    (
        20,
        10,
        'COOL001',
        'NZXT Kraken X53',
        1,
        129.99,
        'Alice Smith',
        'Manager'
    ),
    (
        21,
        11,
        'GPU001',
        'NVIDIA RTX 4070',
        1,
        599.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        22,
        11,
        'PSU001',
        'Corsair RM850x',
        1,
        149.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        23,
        12,
        'SSD001',
        'Samsung 970 EVO Plus',
        2,
        99.99,
        'Alice Smith',
        'Manager'
    ),
    (
        24,
        13,
        'MB001',
        'ASUS ROG B550-F',
        1,
        189.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        25,
        13,
        'CASE001',
        'Lian Li O11 Dynamic',
        1,
        159.99,
        'Bob Garcia',
        'Cashier'
    ),
    (
        26,
        14,
        'GPU002',
        'AMD RX 6800 XT',
        1,
        549.99,
        'Alice Smith',
        'Manager'
    ),
    (
        28,
        14,
        'COOL001',
        'NZXT Kraken X53',
        1,
        129.99,
        'Alice Smith',
        'Manager'
    );

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
    `Type_ID` int(11) NOT NULL,
    `Type` varchar(20) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `type`
--

INSERT INTO
    `type` (`Type_ID`, `Type`)
VALUES (1, 'Admin'),
    (2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
    `ID` int(11) NOT NULL,
    `Employee_ID` int(11) NOT NULL,
    `Username` varchar(50) NOT NULL,
    `Password` varchar(255) NOT NULL,
    `Type_ID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO
    `user` (
        `ID`,
        `Employee_ID`,
        `Username`,
        `Password`,
        `Type_ID`
    )
VALUES (9, 1, 'admin', 'admin', 1),
    (10, 2, 'user', 'user', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
ADD PRIMARY KEY (`Category_ID`),
ADD UNIQUE KEY `unique_category_name` (`Category_Name`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
ADD PRIMARY KEY (`Employee_ID`),
ADD UNIQUE KEY `Email` (`Email`),
ADD KEY `Job_ID` (`Job_ID`);

--
-- Indexes for table `job`
--
ALTER TABLE `job` ADD PRIMARY KEY (`Job_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
ADD PRIMARY KEY (`Product_ID`),
ADD KEY `Supplier_ID` (`Supplier_ID`),
ADD KEY `fk_product_category` (`Category_ID`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier` ADD PRIMARY KEY (`Supplier_ID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction` ADD PRIMARY KEY (`Transaction_ID`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
ADD PRIMARY KEY (`ID`),
ADD KEY `Transaction_ID` (`Transaction_ID`);

--
-- Indexes for table `type`
--
ALTER TABLE `type` ADD PRIMARY KEY (`Type_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
ADD PRIMARY KEY (`ID`),
ADD UNIQUE KEY `Username` (`Username`),
ADD KEY `Employee_ID` (`Employee_ID`),
ADD KEY `Type_ID` (`Type_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
MODIFY `Employee_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
MODIFY `Job_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 22;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
MODIFY `Supplier_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 9;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
MODIFY `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 52;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 52;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
MODIFY `Type_ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`Job_ID`) REFERENCES `job` (`Job_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Supplier_ID`) REFERENCES `supplier` (`Supplier_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction` (`Transaction_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`Employee_ID`) REFERENCES `employee` (`Employee_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`Type_ID`) REFERENCES `type` (`Type_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;