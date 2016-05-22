## Dependencies
- PHP >= 5.5.9
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- composer

MySQL or SQLite

## Installation

- unzip the zip file containing the project
- enter the folder
- run `composer install`
- configure your .env file with your database credentials
- run `php artisan migrate`
- run `php artisan db:seed`
- run `php artisan serve`

## Endpoints
- `GET recipe/{id}/nutrition-info`
- `GET recipe/name/{name}`
- `GET recipe/user/{id}`
- `GET recipe/energy/min/{min}`
- `GET recipe/energy/max/{max}`
- `GET recipe/energy/range/{min}/{max}`
- `POST recipe`
- `PUT recipe/{id}`
- `GET recipe/{id}`
 
## Team
- Eduardo de Brito Colombo <eduardobcolombo@gmail.com>
- Marcos Grimm <marcosgrimm@gmail.com>
- Mathias Grimm <mathiasgrimm@gmail.com>
- Plínio Almeida <plinioalmeida@gmail.com>

