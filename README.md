## Project set up

- To set up this project you will need mysql installed on your machine, or you can spin up docker container as well, both works.
Once You have your mysql up and running create database in it.


- Inside the project root copy .env.example file into the .env file.
Replace the following env variables:
    - DB_HOST - specify the host the databse is running on
    - DB_PORT - specify the correct port the database is running on
    - DB_DATABASE - specify the database name you just created
    - DB_USERNAME - specify the database user
    - DB_PASSWORD - specify the database password


- In terminal in project root run following commands:
  - `composer install`
  - `php artisan key:generate`
  - `php artisan:migrate`
  - `php artisan passport:install`


- Now you will need to put the value of the following env variables:
  - PASSPORT_PERSONAL_ACCESS_CLIENT_ID - in the database in `oauth_clients` table there will be one row with the `"Laravel Personal Access Client"` as a value for the `name` column. Take `id` of that row and put it as a value for this env vriable. Most likely it will be `1` if you didn't store any values in this table before
  - PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET - put the `"Laravel Personal Access Client"` as a value


### Tests

- To run tests suite run the following command in terminal in project root: `./vendor/bin/phpunit`


- Tests will run in the memory database
