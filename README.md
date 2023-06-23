# Cooking with Laravel

## Installation

`composer install`

`sail up -d`

`sail npm run prod`

## Development

`sail up -d`

`sail npm run dev`

Visit <http://localhost/>.

**Login credential (after seeding)**

Username: `test@example.com`\
Password: `password`

## Deployment

### Server setup

To be able to access public uploads a symlink needs to be created after the first deployment.

Run `php artisan storage:link` to create the symlink.

### Deploy changes

[Deployer](https://deployer.org/) is available for easy deployment. Deployer creates a temporary directory on your local 
machine and uploads it to the server. After that it runs the deployment script on the server.You can find the 
configuration in `deploy.php`.

Run `dep deploy` to deploy changes.

## Credits

The seeded recipes are made possible by <https://spoonacular.com/food-api>.
