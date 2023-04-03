# App Billing

Instructions for run this app:

First time

Clone project in your projects folder.
```shell script
$ git clone git@github.com:fatorx/app-billing.git && cd app-billing
```
Copy .env.dist to .env and adjust values in the .env file to your preferences.
```shell script
cp .env.dist .env
```

Mount the environment based in docker-compose.yml.
```shell script
docker-compose up -d --build
```

Access database to create tables (the name app-billing-database is based in the parameter config APP in .env).
```shell script
docker exec -it app-billing-database mysql -u root -p -D billings
```

After access the docker with above command:  
```shell script
source /tmp/dump.sql
```
