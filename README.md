# Athenia App Read Me 

## Project Setup 

In order to get everything ready you will need to make sure that you have vagrant, virtualbox, ansible and the vagrant hosts plugin installed in your system. Once you have that setup run `vagrant up dev` in order to allow the development environment to build.

Once the vagrant has been installed the project dependencies will need to be installed. In order to do this login to the vm with `vagrant ssh dev`, and then run `sudo su` to become the super user. Once you are the super user run `cd /vagrant/code` in order to navigate to the projects root directory. Then run `composer install` in order to install all project dependencies.

The final bit of setup has to do with setting up the remaining environment variables. In order to do this run `cd .env.example .env` from within the project root in the vagrant. Then run `php artisan key:generate && php artisan jwt:secret` in order to generate the application hashes needed. Finally run `php artisan migrate` in order to get the database setup, and finish the setup.

## Swagger

This project uses swagger to generate api documentation. The swagger file is located in docs/swagger.json - and should be double checked whenever changes are made to inline swagger docs.

To generate a fresh copy of swagger.json, run the following command from the root of the project within the VM:

`code/vendor/bin/swagger code/app --output docs/`

Model definitions should all be located at the bottom of a PHP model with all available API properties listed there. Each repository should also have a comment block at the bottom of the class that defines the array variables for swagger.
