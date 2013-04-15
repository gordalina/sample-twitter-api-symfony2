
class package_phpunit
{

  package
  {
    'phpunit':
      ensure  => present,
      require => Package['php5']
  }

}
