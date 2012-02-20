<?php

  include __DIR__ . '/lib/Spl/SplClassLoader.php';
  
  include 'Dropbox/autoload.php';
  $o = new SplClassLoader(null, __DIR__ . '/lib');
  $o -> register();

  
  $config = new Nassau\Config\Config('etc/application.yaml');
  
  $bakery = new Nassau\Bakery\Bakery($config);
