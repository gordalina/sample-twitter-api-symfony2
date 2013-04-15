class package_base
{
  group {
    "puppet": ensure => "present";
  }

  file 
  {
    'apt.config':
      path    => '/etc/apt/apt.conf.d/99aptcache',
      ensure  => present,
      source  => '/vagrant/.puppet/vagrant/resources/apt-config'
  }

  exec
  {
    'init':
      command => 'apt-get update',
      path    => '/usr/bin/',
      require => File['apt.config']
  }

  package
  {
    'htop':
      ensure  => present,
      require => Exec['init']
  }

}
