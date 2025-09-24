# Command instructions

The development environment runs in a docker container using Laravel Sail. When you want to execute a composer, npm or php artisan command, prepend `./vendor/bin/sail` before the command to run it inside the container.

## Example Commands

Here are some example commands you can use:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail composer require
./vendor/bin/sail artisan
```
