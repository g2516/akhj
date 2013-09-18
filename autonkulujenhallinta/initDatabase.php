<?php
require 'bootstrap.php';

$tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
$classes = array(
  $entityManager->getClassMetadata('Akhj\Entity\User')
);
$tool->dropSchema($classes);

$tool->createSchema($classes);

$user = new Akhj\Entity\User();
$user->setFirstname("Teppo");
$user->setLastname("Testaaja");
$user->setEmail("teppo.testaaja@foo.bar");

$user->setPassword(Akhj\Security\PBKDF2::createHash("Salakala1"));

$entityManager->persist($user);

$entityManager->flush();

echo "Testidata luotu onnistuneesti\n";