# Project Setup

Installation

1) Clone bitbucket
	git clone https://github.com/TechnourceDeveloper/TC-review-source-web-api.git
	
2)Install dependency
composer install

3) Copy .env.example and create .env file
   sudo cp .env.example .env
   
4)Generate a new application key
  php artisan key:generate

   
   
5) Environment variables
 Set below varibale for database connection with your database
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=laravel
  DB_USERNAME=root
  DB_PASSWORD=
  
 Set below varibale for SMTP connection for send email
  MAIL_MAILER=smtp
  MAIL_HOST=mailhog
  MAIL_PORT=1025
  MAIL_USERNAME=null
  MAIL_PASSWORD=null
  MAIL_ENCRYPTION=null
  MAIL_FROM_ADDRESS=null
  MAIL_FROM_NAME="${APP_NAME}"
  
6) Migrate and seed database with tablses and predefined data
   php artisan migrate:fresh --seed
   
7) Generate symlink
   php artisan storgae:link
   
8) Start the local development server
   php artisan serve   
   
   You can now access the server at http://localhost:8000

   Testing API
    The api can now be accessed at
    http://localhost:8000/api/v1
    
  


