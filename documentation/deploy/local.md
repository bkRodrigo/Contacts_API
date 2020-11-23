# Docker local env running

## Prequesites
 1. My environment is Ubuntu 20.04 with the latest updates
 1. The host machine must have the following installed
    1. Docker must be installed (I used CE)
    1. Docker compose must be installed
    1. (nice to have) Install Git
    
## Get the local environment running
This isn't an exhaustive step by step, it's more meant to be a recipe to get
everything up and running.
 1. Copy the files to the directory of choice
 1. Open your console app and go to the project root
 1. Go ahead and create a `.env` file in your project root directory. You can
    do this by copying the `.env.example` file located in this project.
    1. In order to facilitate compatibility between the containers and the dev
     environment, we're going to configure the app user to be the same as the
     host's user and id.
       1. In order to get your user and id, just open the console app and type
        `id`. The `id` command will give you your user information; it'll like
        something like this: `uid=1000(toor) gid=1000(toor)...`
       1. In the example above, the user is `toor` and the id is `1000`
    1. Go ahead and configure the `CONTAINER_APP_USER` and the `CONTAINER_APP_ID`
     to the values you respectively obtained in the prior step.
    1. Go ahead and configure the desired values for `DB_DATABASE`, `DB_USERNAME`,
     `DB_PASSWORD`.
    1. In order for the app to function correctly, we'll also need to specify
     the `APP_KEY`; this will be done further down :)
 1. Start the project locally
    1. Make sure you're on the project's root directory.
    1. Build the app first with: `docker-compose build app`
    1. Run the containers in daemon mode: `docker-compose up -d`
    1. Confirm the containers are running with: `docker-compose ps`
    1. Confirm you successfully have your project copied over to the app
     container with: `docker-compose exec app ls -la`
    1. Build the app with: `docker-compose exec app composer install`
    1. Generate an app key with: `docker-compose exec app php artisan key:create`.
     The `key:create` command generates a key for the app. Go ahead and assign
     the value to the `APP_KEY` value that's stored in the `.env` file you
     created earlier.
 1. You should now see the api running in http://localhost:8080
 1. *(Extra)*: Some useful extra commands to work in the local environment
    1. In order to stop the service and keep the current state of the service,
     you can do a `docker-compose pause`; you can then get the service back online
     with a `docker-compose unpause`.
    1. In order to fully stop the service and remove all associated container data,
     you can do a `docker-compose down`; you can get the service back online with a
     `docker-compose up -d`.
    1. You can look at container logs doing: `docker-compose logs`.

 
