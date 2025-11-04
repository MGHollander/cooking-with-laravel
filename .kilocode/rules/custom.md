# Custom rules

## Command instructions

The development environment runs in a docker container using Laravel Sail. When you want to execute a composer, npm or php artisan command, prepend `./vendor/bin/sail` before the command to run it inside the container.

### Example Commands

Here are some example commands you can use:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail composer require
./vendor/bin/sail artisan
```

## Code comments

Never include comments in the code unless absolutely necessary. If you feel like you need to add a comment, consider if the code can be refactored to be more readable instead. Take the example below. These comments do not add anything. The color might change and then the comment must change too. So skip comments like these.

```
.flash-success {
    background-color: #e6ffed; /* Light green */
    color: #2d764a; /* Darker green */
}
```
