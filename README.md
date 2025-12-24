# Cooking with Laravel

Cooking with Laravel is a recipe website made with Laravel and Vue.js. It is a simple project
to save and share recipes. The reason for building this is to collect recipes from the internet,
friends, family and other sources in one place.

## Features

- Create, edit and delete recipes
- Import recipes using [Structured Data](https://schema.org/Recipe)
- Import recipes using [Firecrawl](https://firecrawl.dev) (advanced web scraping with AI)
- Import recipes using Open AI (experimental)
- Search recipes
- Simple user management

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

Run `vendor/bin/dep deploy` to deploy changes.

## Credits

- The seeded recipes are made possible by <https://spoonacular.com/food-api>
- The recipe import feature idea is inspired by <https://fly.io/laravel-bytes/parsing-recipes-with-robot-help/>
- The recipe import feature is made possible by:
  - <https://www.openai.com/>
  - <https://firecrawl.dev/>
- Hero images are made possible by:
  - [Nicole Michalou](https://www.pexels.com/@nicole-michalou/)
  - [Pixabay](https://www.pexels.com/@pixabay/)
  - [cottonbro studio](https://www.pexels.com/@cottonbro/)
- Icons are made possible by:
  - <https://www.svgrepo.com/collection/phosphor-thin-icons>
  - <https://www.svgrepo.com/collection/decathlon-payment-vectors/>
  - <https://www.iconbolt.com/iconsets/voyage-icons/meal>
- Favicons are made possible by <https://realfavicongenerator.net/>
