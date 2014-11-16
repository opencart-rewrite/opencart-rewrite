<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

//TODO the way we get the Path is a bit hackish right now

require_once DIR_SYSTEM . "../../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    array(DIR_SYSTEM . "../../upload"),
    $isDevMode
);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_mysql',
    'user' => DB_USERNAME,
    'password' => DB_PASSWORD,
    'host' => DB_HOSTNAME,
    'dbname' => DB_DATABASE
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
