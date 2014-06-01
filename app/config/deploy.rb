# config valid only for Capistrano 3.1
lock '3.2.1'

set :application, 'symfony2-standard-template'
set :repo_url, 'git@github.com:newism/symfony2-standard-template.git'

# Files that need to remain the same between deploys
# set :linked_files,          []
set :linked_files, [fetch(:app_path) + '/config/parameters.yml']
set :linked_dirs,  [fetch(:log_path), fetch(:web_path) + "/uploads", fetch(:app_path) + "/../vendor"]

set :composer_install_flags, ' --no-interaction --optimize-autoloader'

# Name used by the Web Server (i.e. www-data for Apache)
set :webserver_user,        "www-data"

# Dirs that need to be writable by the HTTP Server (i.e. cache, log dirs)
set :file_permissions_paths, [fetch(:log_path), fetch(:cache_path)]
set :file_permissions_users, ["www-data"]

# Method used to set permissions (:chmod, :acl, or :chown)
set :permission_method,     :acl

# Execute set permissions
set :use_set_permissions,   true

namespace :deploy do
  task :set_symfony_env do
    fetch(:default_env).merge!(symfony_env: fetch(:symfony_env))
  end
end

Capistrano::DSL.stages.each do |stage|
  after stage, 'deploy:set_symfony_env'
end

# curl -sS https://getcomposer.org/installer | php  -- --install-dir=/usr/local/bin --filename=composer
SSHKit.config.command_map[:composer] = "/usr/local/bin/composer"
