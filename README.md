## Set up this project

1. Clone repository
2. Run `composer install`
3. Run `php artisan key:generate`
4. Set up `.env` file
5. Run `php artisan wild:card`
6. Import simpleapi.postman_collection.json in your postman 
7. Run and test :D

## API Url List
1. index[GET] - `http://127.0.0.1:8080/api/person`
2. single[GET] - `http://127.0.0.1:8080/api/person/1`
3. multi[GET] - `http://127.0.0.1:8080/api/multi?ids=1,2,3`
4. insert[POST] - `http://127.0.0.1:8080/api/person`
5. update[PUT] - `http://127.0.0.1:8080/api/person/1`
