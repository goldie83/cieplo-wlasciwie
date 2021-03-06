lock '3.4.0'

set :application, 'cieplowlasciwie.pl'
set :repo_url, 'git@github.com:juzefwt/cieplo-wlasciwie.git'

set :assets_install_flags, '--symlink --relative'

set :linked_files, fetch(:linked_files, []).push('app/config/parameters.yml')
set :linked_dirs, [fetch(:log_path), 'web/uploads', 'web/media', 'node_modules', 'bower']

set :use_set_permissions, true
set :webserver_user,    "www-data"
set :use_sudo, true
set :permission_method, :acl

set :keep_releases, 10

set :file_permissions_users, ['www-data']
set :file_permissions_paths, [fetch(:log_path), fetch(:cache_path), 'web/uploads']

after 'symfony:clear_controllers', 'symfony:assets:install'
