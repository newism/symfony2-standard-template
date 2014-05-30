# config valid only for Capistrano 3.1
lock '3.2.1'

set :application, 'symfony2-standard-template'
set :repo_url, 'git@github.com:newism/symfony2-standard-template.git'

# Files that need to remain the same between deploys
# set :linked_files,          []
set :linked_files, [fetch(:app_path) + '/config/parameters.yml']

# curl -sS https://getcomposer.org/installer | php  -- --install-dir=/usr/local/bin --filename=composer
SSHKit.config.command_map[:composer] = "/usr/local/bin/composer"
set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader'


namespace :deploy do

    task :copy_vendors do
        on release_roles(:all) do
            releases = capture(:ls, '-x', releases_path).split
            if releases.count
                info t("Copying vendors from previous release")
                exec "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{latest_release}; fi;"
            end
        end
    end

end
