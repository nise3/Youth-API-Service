# NISE3 Youth Management API Service
 
## The project deployment Notice

## Run lumen
```shell
php -S localhost:8005 -t public
```
## For New Release use the following in Merge Request
```shell
RELEASE = 'php artisan migrate:fresh --seed && php artisan cache:clear'
RELEASE = 'php artisan cache:clear'
RELEASE = 'php artisan list'
```


## RabbitMQ consume command
```shell
php artisan queue:work --queue=youth.course.enrollment.q,youth.rpl.application.q
```

