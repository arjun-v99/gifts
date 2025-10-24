Steps to Run
1. Make sure laravel is PHP, composer and laravel installed in your system
2. Clone the repository from Github
3. Run the command `php artisan migrate` from your project root folder.
4. Run the command `php artisan db:seed --class=AdminUserSeeder` from your project root folder.
5. Run the command `php artisan serve` at root folder
6. For Login Access `http://127.0.0.1:8000/api/login` route
7. Use `passsword` and `email` as request body.
8. For Default users use `admin@example.com` and `admin` as email and password respectively.
9. For creating Gift use `http://127.0.0.1:8000/api/gifts/create` with `receiver_id` as request body and set `Authorization` Header in your request, use `Bearer token_value_received_from_login_api_here` to send the request