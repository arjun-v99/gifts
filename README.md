Steps to Run
1. Make sure laravel, PHP v8+ and composer are installed in your system
2. Clone the repository from Github
3. Run the command `php artisan migrate` from your project root folder.
4. Run the command `php artisan db:seed --class=AdminUserSeeder` from your project root folder.
5. Run the command `php artisan serve` at root folder
6. For Login Access `http://127.0.0.1:8000/api/auth/login` route
7. Use `passsword` and `email` as request body.
8. For Default users use `admin@example.com` and `admin` as email and password respectively.
   Or use `user2@example.com` and `12345` as email and password respectively.
9. Save the Token received as response to successfully invoke other API endpoints
10. For creating Gift:
   - Set the endpoint to `http://127.0.0.1:8000/api/gifts`
   - Use `POST` as HTTP Method
   - Set `Authorization` Header in Request. Set `Bearer use_token_received_login_api_here` as the value
   - In the Request body set `receiver` key and a value for which gift should be created. value should be a email address