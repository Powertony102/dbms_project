# Database-Driven Intelligent Restaurant Management System
COMP3013 Database Management System Final Project

## Project Description

This project aims to develop a modern restaurant management system that addresses the common real-life challenges faced by restaurants in handling reservations, table allocation, and service coordination. The system solves the inefficiency and rigidity of traditional restaurant management systems, making it easier to track table availability, manage reservations, handle special customer requests, and keep staff updated in real time.

## System Features

- **Role-Based Access Control**: Administrators, receptionists, cleaners, and customers have different access privileges
- **Table Reservation Management**: Customers can view and reserve different types of tables
- **Order System**: Supports online ordering with real-time price updates
- **Wallet Function**: Customers can recharge and use their account balance for payments
- **Automated Triggers**: Automatically update order amounts and table status
- **Comment Feedback**: Customers can provide feedback on services

## Database Design
![db_design](/doc/db_design.png)

### ER Diagram
![ER Diagram](/doc/report/ER.png)

### Main Entities
- **User**: Common information for all users, including administrators, receptionists, cleaners, and customers
- **Table**: Physical table information and cleaning status
- **Table Type**: Specifications and prices for different types of tables
- **Menu**: Information about available dishes
- **Order**: Records customer orders and their status
- **Comment**: Customer feedback information

## User Interface

The system includes multiple interfaces designed for different roles:
- **Customer Pages**: home.php, book.php, order.php, order-detail.php, credit.php
- **Administrator Page**: admin.php
- **Receptionist Page**: receptionist.php
- **Cleaner Page**: cleaner.php

## Technical Implementation

### Trigger Functions
- Automatically update the total order amount
- Record order status changes
- Automatically deduct inventory
- Ensure data integrity

### SQL Functionalities
- User registration and login
- Query table types and details
- Create and manage orders
- Query and update wallet balance
- Manage table cleaning status

## Team Members
- Li Xinze (2330026083) - Code and database implementation
- Li Jiale (2330026073) - Code and database implementation
- Tian Zhiwen (2230033036) - ER diagram design, trigger implementation, report writing
- Yan Shan (2230033048) - Data insertion

## Installation and Usage

[Add system installation and usage instructions here]
