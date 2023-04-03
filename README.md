# App Billing

This system aims to control billings and manage payments of these billings.

------



## Functionalities

- Receive CSV files and generate "bills" based on the data
- Send emails charging bills
- Receive and process payments for bills via webhook
------

### Sequence Receive File

```mermaid
    sequenceDiagram
    ReceiveFile ->> EndPoint: Send CSV
    EndPoint ->> Storage: Send Zip File
    Storage ->> EndPoint: Send Key UUID File
    EndPoint ->> ReceiveFile: Return Key UUID File
    EndPoint ->> ProducerFile: Send UUID as a Message
    ProducerFile -) ConsumerFile: Send a serialized PHP object
````
------
### Technologies
- PHP 8.2 with Laminas
- MySQL 8.0.32
- RabbitMQ 3.10.1

------

## Instructions for run this app:

### First time

Clone project in your projects folder.
```shell script
$ git clone git@github.com:fatorx/app-billing.git && cd app-billing
```
Copy .env.dist to .env and adjust values in the .env file to your preferences.
```shell script
cp .env.dist .env 
```

Add permissions to folder data, this is where the persistence files will be kept.
```shell script
chmod 755 data
```

Mount the environment based in docker-compose.yml.
```shell script
docker-compose up -d --build
```
Access database to create tables (the name app-billing-database is based in the parameter config APP in .env).
```shell script
docker exec -it app-billing-database mysql -u root -p -D billings
```
After access the docker with above command, at the MySQL prompt type:  
```shell script
source /tmp/dump.sql
```

------
### Working routine 
```shell script
docker-compose up -d
```
------

### Access to environment
###
Test to send a file:
```shell script
curl --location '0.0.0.0:8009/v1/billing/send-file' \
--form 'file=@"/home/yourpath/projects/app-billing/temp/test_length_ok.csv"'
```
###
Test to send a request to webhook (file size limited in 1MB):
```shell script
curl --location '0.0.0.0:8009/v1/billing/webhook' \
     --header 'Content-Type: application/json' \
     --data '{
        "debtId": "123",
        "paidAt": "2022-06-09 10:00:00",
        "paidAmount": 1001.10,
        "paidBy": "John Doe"
     }'
```
------
### Consumers 
```shell script
docker exec -it app-billing-php-fpm bash php bin/cli.php /v1/billing/consumer-files
```
```shell script
docker exec -it app-billing-php-fpm bash php bin/cli.php /v1/billing/consumer-lines
```
```shell script
docker exec -it app-billing-php-fpm bash php bin/cli.php /v1/billing/consumer-emails
```
```shell script
docker exec -it app-billing-php-fpm bash php bin/cli.php /v1/billing/consumer-payments
```
------

### Tests Inside Docker 
```shell script
docker exec -it app-billing-php-fpm bash
```
And then do this
```shell script
vendor/bin/phpunit --testdox --testsuite "Billing Test Suite"
```
or 
```shell script
vendor/bin/phpunit --testdox --testsuite "Billing Test Suite" --group billing
```

------
### Tests Outside Docker
```shell script
docker exec -it app-billing-php-fpm vendor/bin/phpunit --testdox --testsuite "Billing Test Suite"
```

------
### Coverage
```shell script
docker exec -it app-billing-php-fpm bash 
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html data/report --group billing
```

------
## Licence

[MIT](https://github.com/fatorx/php-gamer/blob/main/LICENSE.md)


