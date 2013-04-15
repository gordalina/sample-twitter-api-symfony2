
class package_app_server
{

  package
  {
    'nfs-kernel-server':
      ensure => present,
  }

  service
  {
    'nfs-kernel-server':
      ensure => running,
      enable => true,
      hasstatus => true,
      require => Package['nfs-kernel-server'],
  }

  package
  {
    'sendmail':
      ensure  => present
  }

  package
  {
    'mysql-server':
      ensure  => present
  }

  package
  {
    'mysql-client':
      ensure  => present
  }

  package
  {
    'apache2':
      ensure  => present,
      require => Package['mysql-server']
  }

  package
  {
    'php5':
      ensure  => present,
      require => Package['apache2']
  }

  package
  {
    'php-apc':
      ensure  => present,
      require => Package['php5']
  }

  package
  {
    'php5-xdebug':
      ensure  => present,
      require => Package['php5']
  }

  package
  {
    'php5-curl':
      ensure  => present,
      require => Package['php5']
  }

  package
  {
    'php5-sqlite':
      ensure  => present,
      require => Package['php5']
  }

  package
  {
    'phpmyadmin':
      ensure  => present,
      require => Package['php5']
  }

  package
  {
    'php5-cli':
      ensure  => present,
      require => Package['php5']
  }

  package
  {
    'php-pear':
      ensure  => present,
      require => Package['php5-cli']
  }

  package
  {
    'default-jre':
      ensure  => present
  }

  package
  {
    'zip':
      ensure  => present
  }

  file
  {
    'pear.tmpdirfix.prepare':
      ensure  => directory,
      path    => '/tmp/pear',
      require => Package['php-pear']
  }

  file
  {
    'pear.tmpdirfix':
      ensure  => directory,
      path    => '/tmp/pear/cache',
      mode    => 777,
      require => File['pear.tmpdirfix.prepare']
  }

  exec
  {
    'pear.upgrade.pear':
      path => '/bin:/usr/bin:/usr/sbin',
      command => 'pear upgrade PEAR',
      require => File['pear.tmpdirfix']
  }

  file
  {
    '/vagrant/app/logs/apache2':
      ensure => "directory"
  }

}
