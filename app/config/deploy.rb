# config valid only for Capistrano 3.1
lock '3.2.1'

set :application, 'symfony2-standard-template'
set :repo_url, 'git@github.com:newism/symfony2-standard-template.git'

# curl -sS https://getcomposer.org/installer | php  -- --install-dir=/usr/local/bin --filename=composer
SSHKit.config.command_map[:composer] = "/usr/local/bin/composer"
set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader'

namespace :deploy do

  before :starting, 'composer:install_executable'

  desc 'Restart application'
  task :restart do
    on roles(:app), in: :sequence, wait: 5 do
      # Your restart mechanism here, for example:
      # execute :touch, release_path.join('tmp/restart.txt')
    end
  end

  after :publishing, :restart

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

end
