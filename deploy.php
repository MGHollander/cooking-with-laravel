<?php

namespace Deployer;

use Deployer\Exception\Exception;

require 'recipe/laravel.php';

// Config

set('branch', 'main');
set('default_stage', 'production');
set('http_user', 'kokenv2');
set('local_deploy_path', 'tmp_deploy');
set('repository', 'git@github.com:MGHollander/cooking-with-laravel.git');
set('writable_mode', 'chmod');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('koken.maruc.nl')
    ->set('remote_user', 'kokenv2')
    ->set('deploy_path', '~/domains/koken.maruc.nl');

// Hooks
desc('Prepares a new release');
task('deploy:update_code', function () {
    if ($branch = input()->getOption('branch')) {
        set('branch', $branch);
    }
    if ($tag = input()->getOption('tag')) {
        set('branch', $tag);
    }

    if (testLocally('[ -d {{ local_deploy_path }} ]')) {
        writeLn('Found a local deploy dir. Remove it!');
        runLocally('rm -rf {{ local_deploy_path }} ');
    }

    if (! testLocally('git show-ref {{ branch }} >/dev/null 2>&1')) {
        throw new Exception('No Git reference to '.get('branch').' found.');
    }

    writeLn('Clone <info>{{ branch }}</info> into <info>{{ local_deploy_path }}</info>');
    runLocally('git clone {{ repository }} {{ local_deploy_path }} --branch {{ branch }} --single-branch --depth=1');

    writeLn('Install composer packages locally');
    runLocally('cd {{ local_deploy_path }} && composer install');

    writeLn('Install node modules locally');
    runLocally('cd {{ local_deploy_path }} && npm install');

    writeLn('Build front-end locally');
    runLocally('cd {{ local_deploy_path }} && npm run build');

    writeLn('Remove files that should not be uploaded');
    runLocally('rm -rf {{ local_deploy_path }}/{.editorconfig,.env.example,.git,.gitattributes,.gitignore,node_modules,vendor}');

    writeLn('Upload files to <info>{{ release_path }}</info>');
    runLocally('scp -rC {{ local_deploy_path }}/* {{ hostname }}:{{ release_path }}');

    writeLn('Remove local deploy dir');
    runLocally('rm -rf {{ local_deploy_path }}');
});

after('deploy:failed', 'deploy:unlock');
