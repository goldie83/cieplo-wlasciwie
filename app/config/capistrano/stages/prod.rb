server '185.25.149.41',
  user: 'wtreter',
  roles: %w{app web csm mem db},
  ssh_options: {
    forward_agent: true,
    auth_methods: %w{publickey}
  }

set :deploy_to, '/sites/cieplowlasciwie.pl'
set :branch, 'master'
set :symfony_env, 'prod'
