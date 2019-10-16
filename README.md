LGPL-3.0-or-later

# Histologie

This application works with : 

 * **PHP** version 5.6 or higher
 * **MySQL** version 5.7 or higher
 * **Apache** or **Nginx**

## Configuration

There are two files to change for application's configuration :

 * **`/.env`** is for Docker environment variables to connect MySQL database.
 * **`/config/config.cfg`** for the rest of config variables needed.

The models for these files are available with a `.dist` extension. Simply remove the extension and change 
the example values with yours.

## Get Docker

If you already have Docker installed, skip to the next step.

This application is dockerized, so you don't need to install PHP MySQL nor Apache to run it. The only require is
**Docker** and **docker-compose**, and you can find a shell script to install it automatically.

Run the following command from project's root:

```bash
sudo chmod +x docker/install-docker.sh
docker/install-docker.sh
```

To check if installation is fine run :

```bash
docker-compose --version
```

## Installation 

### PHP service

This part is important to setup your PHP environment. First you'll have to create the logs' folder from the container 
itself, and then run a `composer install` to setup the project's dependencies.

Execute the following command to create the logs' folder with the correct rights :

```bash
docker-compose run php /bin/bash -c "mkdir -p /web/logs /web/commande/pdf; \
                chown -R www-data:www-data /web/logs /web/commande/pdf; \
                cd /web && composer install;"
```

### Run the app

Before running the application,you must ensure that the **HTTP and/or HTTPS default ports are available**.
Ensure that you don't have an Apache or a Nginx service already using 80 and/or 443 ports.
If everything is OK you can now run your app :

```bash
docker-compose down
docker-compose up -d
```

To test, simply go to the application URL.

### Setup database

#### Empty scheme

To create the database scheme execute the following command **while MySQL service is running** :

```bash
docker-compose exec mysql sh -c 'mysql -u "$MYSQL_USER" \
        --database="$MYSQL_DATABASE" \
        --password="$MYSQL_PASSWORD" < /web/bdd/empty-scheme.sql'
```

This will create the empty scheme with a default admin account with the following credentials :

 * **username** : `admin`
 * **password** : `admin`

I strongly advice you to change those credentials, or to create your own admin account before deleting this one,
when you first logged into the application.

#### Migrate database

If you want to migrate your current database to the MySQL service, you need to : 

 * Export your current database as a `.sql` file
 * Put it inside the  `bdd/` folder
 * Run the previous command by replacing the `empty-scheme.sql` file by your's
