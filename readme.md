# NerdyGadgets Webshop

Welcome to the NerdyGadgets webshop project. This is a comprehensive e-commerce platform built with PHP, designed to provide a seamless online shopping experience for tech enthusiasts and gadget lovers.

At its core, NerdyGadgets is a robust and user-friendly webshop that features a wide range of products, from the latest tech gadgets to classic nerd culture memorabilia. The platform is optimized to deliver a smooth browsing experience, with features like product search, detailed product information, shopping cart functionality, and secure checkout process.

## Requirements

To run this project, you will need the following:

- PHP 7.4 or higher: The project is built with PHP. You can download it from [here](https://www.php.net/downloads.php).
- Composer 2 or higher: This project uses Composer for managing PHP dependencies. You can download it from [here](https://getcomposer.org/download/).
- ngrok: This is required for transaction testing. You can download it from [here](https://ngrok.com/download).
- 10.4.27-MariaDB or higher: The project uses MariaDB for its database. You can download it from [here](https://downloads.mariadb.org/).

Please ensure that you have these installed before proceeding with the setup.

## Getting Started

To get the project up and running on your local machine, follow these steps:

1. Clone the repository:
   - `git clone https://github.com/stefanpost268/NerdyGadgets.git`

2. Navigate to the project folder and install the dependencies:
   - `cd nerdygadgets`
   - `composer install`

3. Copy the example environment file and make the required configuration changes:
   - `cp .env.example .env`

4. Run the project:

Please note that you need to have PHP and Composer installed on your machine to run this project.

## Features

The NerdyGadgets webshop includes the following features:

- Product Browsing: Browse through a wide range of products with detailed descriptions and images.
- Shopping Cart: Add products to your cart and manage your orders.
- Checkout: Securely complete your purchase with integrated payment processing.

We hope you enjoy using the NerdyGadgets webshop and find it useful for your online shopping needs. Happy shopping!

## Frequently Asked Questions

### Q: Why are my classes not getting recognized?

**A:** This is a common issue that can occur if the Composer autoload file is not up-to-date. To resolve this, you need to run the following command in your terminal:

```bash
composer dump-autoload
``````

### Q: Why is the checkout not working on localhost?

**A:** The checkout process requires a public URL for payment processing callbacks. When working on localhost, this can be achieved by using a service like ngrok. To start an ngrok service, run the following command in your terminal:

On Linux
```bash
ngrok http 80
``````

On windows
```bash
ngrok.exe http 80
``````

### Q: Why am I seeing a "website wordt onderhouden" message?

**A:** This message is displayed when the application cannot connect to the database or retrieve products correctly. To resolve this, ensure that your database is set up correctly as per the instructions in the "Getting Started" section. Make sure that the database connection details in your `.env` file are correct and that the necessary tables and data exist in your database.

### Q: Why am i getting the message "Table 'nerdygadgets.user' doesn't exist" on the checkout page?

**A:** This message is displayed because the website is missing the required database migrations to run the checkout system. To run the migrations go to Database/Migrations/readme.md for moore info.

