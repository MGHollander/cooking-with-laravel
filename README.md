# Cooking with Laravel

## Installation

`composer install`

`sail up -d`

`sail npm run prod`

## Development

`sail up -d`

`sail npm run dev`

Visit <http://localhost/>.

There is an admin available at <http://localhost/dashboard>.

**Login credential (after seeding)**

Username: `test@example.com`\
Password: `password`

## Deployment

### Server setup

To be able to access public uploads a symlink needs to be created after the first deployment.

Run `php artisan storage:link` to create the symlink.

## Credits

The seeded recipes are made possible by <https://spoonacular.com/food-api>.
