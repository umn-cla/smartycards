<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';

// Config
set('repository', 'https://github.com/umn-cla/smartycards-api.git');
set('ssh_type', 'native');
set('update_code_strategy', 'clone');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('dev')
    ->set('hostname', 'cla-smartycards-dev.oit.umn.edu')
    ->set('remote_user', 'latis_deploy')
    ->set('labels', ['stage' => 'dev'])
    ->set('deploy_path', '/var/www/html/');

// Hooks
after('deploy:update_code', 'npm:install');
task('assets:generate', function () {
    cd('{{release_path}}');
    run('npm run build');
})->desc('Assets generation');
after('deploy:vendors', 'assets:generate');
after('deploy:failed', 'deploy:unlock');
