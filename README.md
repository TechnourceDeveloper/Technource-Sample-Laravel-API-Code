# Technource Sample Laravel API Code 

This is a code sample from one of our projects for review. APIs are developed in Laravel.
# Installation

1) Clone bitbucket
```bash
    git clone https://github.com/TechnourceDeveloper/TC-review-source-web-api.git
```	
2) Install dependency
```bash
   composer install
```
3) Copy .env.example and create .env file
```bash 
   sudo cp .env.example .env
```   
4) Generate a new application key
```bash
   php artisan key:generate
```
   
   
5) Environment variables in .env

  For update .env file using cmd use below command 
  
  sudo nano .env

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
```bash
   php artisan migrate:fresh --seed
```   
7) Generate symlink
```bash 
   php artisan storgae:link
```   
8) Start the local development server
```bash
   php artisan serve   
```   
   You can now access the server at http://localhost:8000

   Testing API
   
    The api can now be accessed at
    
    http://localhost:8000/api/v1
    
  


