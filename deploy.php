<?php
namespace Deployer;

require 'recipe/laravel.php';

// PHP FPM 

set('php_fpm_service', 'php7.2-fpm');
set('php_fpm_command', 'echo "" | sudo -S /usr/sbin/service {{php_fpm_service}} reload'); // for setups like laravel forge
set('php_fpm_command', "kill -USR2 $(ps -ef | grep '[p]hp-fpm: master' | awk '{print $2}')"); // for docker based setups with laradock or similar

// Configuration
set('repository', 'git@github.com:Larastudio/lslaravel.git');
set('default_stage', 'production');
set('git_tty', true); // [Optional] Allocate tty for git on first deployment
set('ssh_type', 'native');
set('keep_releases', 10);

// Make sure uploads & published aren't overwritten by deploying
set('shared_dirs', []);
set('shared_files', [
    '.env',
]);
set('writable_dirs', [
    'storage/framework/cache/data',
]);

// SMART CUSTOM DEPLOY COMMANDS
task('db:migrate', function () {
    run("cd {{release_path}} && php artisan migrate");
});
task('horizon:terminate', function () {
    run("cd {{release_path}} && php artisan horizon:terminate");
});

// Hosts
// dep deploy production
// dep deploy staging

   host('staging')
   ->hostname('staging.larastud.io')
   ->user('web')
   ->forwardAgent()
   ->stage('staging')
   ->set('deploy_path', '/opt/easyengine/sites/staging.lara.studio');

   host('production')
   ->hostname('larastud.io')
   ->user('web')
   ->forwardAgent()
   ->stage('production')
   ->set('deploy_path', '/opt/easyengine/sites/lara.studio');


// Run database migrations
after('deploy:symlink', 'db:migrate');
after('deploy', 'fpm:reload');



