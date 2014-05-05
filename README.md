uiuc-cs428-cms
==============

UIUC CS 428 - Conference Management System

Conference Management System provides users and conference administrators to enroll and manage conferences.

## Contributors
Alexander Hadiwijaya (hadiwij2@illinois.edu)
Derek Quach (dlquach2@illinois.edu)
Eunsoo Roh (roh7@illinois.edu) 
Han Jiang (hjiang25@illinois.edu)
Jiangchuan Zhou (jzhou31@illinois.edu)
Kirill Mangutov (manguto2@illinois.edu)

## Prerequisites
* PHP 5.5+ with mcrypt and cURL
* MySQL 5
* [Composer](https://getcomposer.org/)
* Apache 2 / nginx (optional, strongly recommended for production services)
* [UiucCmsUiPayPaymentBundle](https://github.com/toukoaozaki/UiucCmsUiPayPaymentBundle)

## Installation
1. Make sure all prerequisites are installed and ready.
2. Download the source code from the repository. git clone is recommended:

        git clone https://github.com/uiuc-cms/uiuc-cs428-cms.git

3. Use composer to install the dependencies (vendor).

        composer.phar install
        
4. Setup initial configuration by following the interactive prompt. For iPay-related parameters, contact Merchant Payment Services at (217) 244-9384.
5. Use the following commands to set up your initial database and assets:

        app/console doctrine:database:create
        app/console doctrine:schema:create --force
        app/console assetics:dump
        app/console assets:install
        app/console doctrine:fixtures:load
        
6. Run the server

        app/console server:run

For setting up with Apache/nginx, use the same setup commands with --env=prod suffix. Make sure your DocumentRoot points to the web/ directory, then you're all set.
