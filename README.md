## Set up this project

1. Clone repository
2. Run `composer install`
3. Run `php artisan key:generate`
4. Set up `.env` file
5. Run migrations `php artisan migrate`
6. Seed the table `php artisan db:seed`
7. Run project `php artisan serve`

## API Url List
1. index[GET] - `/api/person`
2. single[GET] - `/api/person/1`
3. insert[POST] - `/api/person`
4. update[PUT] - `/api/person/1`
