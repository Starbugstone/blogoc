# blogoc

## Description
An OOP MVC blog motor created for the open classrooms class.

## Requirements

* MySQL or MariaDB Database
* PHP minimum version 7.1

## Installation - Downloading
First you need to download or clone the project and install composer.
https://getcomposer.org/doc/00-intro.md

navigate into the project folder and execute `composer -install` to install all the external libraries.

### external libraries used
* twig
* slugify
* bootstrap-fileinput
* swiftmailer
* htmlpurifier

#The following section is how to install the motor.
It may seam a bit complicated but this is just a temporary solution to get you up and running, an install script is due in version 1.1.

## Installation - The Database
once all is downloaded, we need to install the database. The project includes a basic SQL file called TablesStructure.sql in the DataBase folder.
This file has been modified from the original structure to include a prefix *"boc_"* to each table. This enables to install the tables into an existing database if needed.
Feel free to remove or replace all *"boc_"* from the SQL script if you do not wish to use this prefix.

The database includes the default Admin user and the configuration tables. We shall update these once the motor is running.

To enable the site to connect to the database, we need to update the `Config.php` file in the **Core** folder.
```php
    //The Database host, check with your hosting
    const DB_HOST = 'localhost';
    
    //The Database Name, check with your hosting
    const DB_NAME = 'blogoc';
    
    //The Database user, check with your hosting
    const DB_USER = 'root';
    
    //The Database Password, check with your hosting
    const DB_PASSWORD = '';

    // Set this to false for production envirmonment.
    // When set to true, we have more verbos error messages, a dev helper pannel and twig cache deactivated.
    const DEV_ENVIRONMENT = true;

    // The table prefix, with the SQL script, we need to add our "boc" prefix like below
    const TABLE_PREFIX = 'boc';

    // The folders for image uploading, they are all in the Public filder.
    // This shouldn't need to be changes
    const UPLOADED_IMAGES = "uploaded_images/";
    const CONFIG_IMAGES = "config_images/";
    const USER_IMAGES = "user_images/";

    // If you wish to add a google recapche V2 to the "contact me" form, set your Secret and Public keys here.
    // Leave the public key blank to deactivate the Recapcha.
    const GOOGLE_RECAPCHA_SECRET_KEY = "";
    const GOOGLE_RECAPCHA_PUBLIC_KEY = "";
```



## Install - redirect to the public fOlder.
The blog motor does not accept sub folder installation (Eg. *myhost.com/blog/*). you need to create an alias/subdomaine (Eg. *blog.myhost.com*).
You then need to configure your apache server to **redirect the alias to the public folder**. From there, the given .htaccess will decompose the url and call the required components for the site to work.

Once all this is done, you should be able to connect to the front page of your new site.

## Install - Connect the admin and securing the site.
The default Admin is registered to **admin@me.com** with the password **admin1234**.
It is recommended to **change the email address** to your personal email address in the database before going any further.

then connect to the admin section of the site (login). From here you will be able to change the password.

The site requires at least **8 characters with letters and numbers**. For the admin account I recommend something a bit more secure like adding **special characters** to the password.

The site has a built in anti brute force mechanism, if a bad password is typed more than 3 times, the user is locked out for 5 minutes.

## Install - Configuring the Site
In the site configuration section, you can update all the variables of the site.
The front page text and social icons are self explanatory and i will not go over them in detail.

1. **Front page other**

* The about me image should be you logo or photo, it will appear in the about me section and be linked to the CV file.

2. **global site configuration**

* Site name is what is shown on all pages and also sent via mail
* No image replacement is the image that will be shown when no Image is configured (like a user avatar or a post logo).
* Admin email address is the main address used to send alerts and messages from the contact form

3. **SMTP configuration**

* Here you need to set all the SMTP details given by your hosting service. If you do not have a SMTP server, I recommend using [mailjet](https://www.mailjet.com/).
* To make sure that the  SMTP server is working, you can click the "*Send test mail*" button.

## install - Updating the constants

The `Constant.php` file in the **Core** folder contains all the hard written variables used in the site. You can update these if needed

```php
    // Do not change these unless you wish to add extra roles (not fuly implemented yet)
    const ADMIN_LEVEL = 2;
    const USER_LEVEL = 1;

    // the number of posts on the front page, posts in categories, backend lists and comments per page
    const FRONT_PAGE_POSTS = 3;
    const POSTS_PER_PAGE = 4;
    const LIST_PER_PAGE = 10;
    const COMMENTS_PER_PAGE = 2;

    // Each post has an excerpt generated from the first 50 words of the text (unless a read more tag is used). You can change the number of words here
    const EXCERPT_WORD_COUNT =50;

    //login security, number of bad passwords untill lockout ant the lockout duration
    const NUMBER_OF_BAD_PASSWORD_TRIES = 3;
    const LOCKOUT_MINUTES = 5;

    // the time a user has to click the reset password link sent via email
    const PASSWORD_RESET_DURATION = 240;//number of minutes the reset password link is valid

    //The hash used to generate login tokens. It is recommended to change this to a random string for security reasons.
    const HASH_KEY = "test1234";
```

