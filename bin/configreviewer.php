<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dir = 'config/default/';
if (defined('STDIN')) {
  if(isset($argv[1])){
    $dir = $argv[1];
  }
}
else {

}
require_once '../../../autoload.php';

require_once('../src/ConfigReader.php');

$loader = new \Twig\Loader\FilesystemLoader(realpath(dirname(__FILE__) . '/..'));
$twig = new \Twig\Environment($loader, [
  'debug' => TRUE,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());


$traverse = '../../../../';

$cr = new ConfigReader($traverse.$dir);

$settings = $cr->getSettings();
$key_extensions = $cr->getExtensions();
$views = $cr->getViews();
$paragraphs = $cr->getParagraphs();
$webforms = $cr->getWebforms();
$taxonomies = $cr->getTaxonomies();
$contenttypes = $cr->getContentTypes();

$page = $twig->render('templates/main.html.twig', [
  'settings' => $settings,
  'path' => $dir,
  'wfs' => $webforms,
  'taxonomies' => $taxonomies,
  'views' => $views,
  'extensions' => $key_extensions,
  'cts' => $contenttypes,
  'taxonomies' => $taxonomies,
  'paragraphs' => $paragraphs,
]);

if (!defined('STDIN')) {
  echo $page;
}

$review = fopen("../../../../config.htm", "w") or die("Unable to open file!");
fwrite($review, $page);
fclose($review);




