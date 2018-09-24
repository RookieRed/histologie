# Histologie

## Configuration

Change the environment values in the `cfg/config.cfg` and `cfg/config.stable.cfg` files.

## Installation

This application works with : 

 * **PHP** version 5 or higher
 * **MySQL** version 5 or higher
 * **Apache** or **Nginx**

### Install Docker

If you already have Docker installed, skip to the next step.

This application is dockerized, so you don't need to install PHP MySQL nor Apache to run it. The only require is
**Docker** and **docker-compose**, and you can find a shell script to install it automatically.

Run the following command from project's root:

```bash
./docker/install-docker.sh
```

To check if installation is fine run :

```bash
docker-compose --version
```

### Build 

To build the project execute the command from project's root :

```bash
docker-compose build
```

### Logs directory

This command will build the PHP image. Then you need to execute the following command to create the logs' folder with
the correct rights :

```bash
docker-compose build
docker-compose run php /bin/bash -c "cd /web; \
                mkdir logs; \
                chown www-data:www-data logs;"
```

### Run the app

If evrything is OK you can now run your app :

```bash
docker-compose up
```

To test, type the address of your application
