# NISE3 Industry And Industry Association API Service

## Official Documentation

#### Following 3 branches are protected:
```shell
<<<<<<< HEAD
master
staging
develop
=======
php -S localhost:8005 -t public
```
## For New Release use the following in Merge Request
```shell
RELEASE = 'php artisan migrate:fresh --seed && php artisan cache:clear'
RELEASE = 'php artisan cache:clear'
RELEASE = 'php artisan list'
>>>>>>> origin/develop
```
```master``` branch is only being used for production release.
Code of master branch has not been used. We are using container image from staging branch to deploy to production.
Common configurations should be placed in the ```.env.example``` file.



#### Development Environment
Sensitive information like different type of credentials for development environment have to be configured in the file named ```deploy/values.dev.yaml```.
In order to release in the development environment, we have to switch to ```develop``` branch and after that we have to increase the value of the property ```devBuildImageVersion``` in ```deploy/version.yaml```.
After that we have to write a commit message ```RELEASE = 'php artisan migrate'``` if we want to do any database migration after the release. But, if we don't
want to run any command after the release then the commit message will be ```RELEASE = 'php artisan list'```.

#### Staging Environment
Sensitive information like different type of credentials for staging environment have to be configured in the file named ```deploy/values.staging.yaml```.
In order to release in the staging environment, we have to switch to ```staging``` branch and after that  we have to increase the value of the property ```stagingBuildImageVersion``` in ```deploy/version.yaml```.
After that we have to write a commit message ```RELEASE = 'php artisan migrate'``` if we want to do any database migration after the release. But, if we don't
want to run any command after the release then the commit message will be ```RELEASE = 'php artisan list'```.

#### Production Environment
Sensitive information like different type of credentials for production environment have to be configured in the file named ```deploy/values.production.yaml```.
In order to release in the production environment, we have to switch to ```master``` branch and after that  we have to set successful version of the staging environment value of ```stagingBuildImageVersion``` and
make a git push. This will successfully release the desired version of staging environment container image to production environment.

#### Initial Setup
After deploying at the first time, seeder files have to be run by running the following command:
```php artisan db:seed```

#### RabbitMQ consume command. This following command has to be run in the background as a daemon service.
```shell
php artisan queue:work --queue=youth.course.enrollment.q,youth.rpl.application.q
```

## Contributing


## Security Vulnerabilities

## License
Softbd LTD








