<?php

namespace Morpht;

/*
 * @ToDo: Add other reader functions
 */
use Spyc;

/**
 * Class ConfigReader
 */
class ConfigReader {

  public function __construct($configdir) {
    $this->configdir = $configdir;
    $this->allfiles = scandir($configdir);
  }

  /**
   * @return \stdClass
   */
  public function getSettings() {

    $settings = new \stdClass();
    $settings->performance = Spyc::YAMLLoad($this->configdir . 'system.performance.yml');
    $settings->extensions = Spyc::YAMLLoad($this->configdir . 'core.extension.yml');
    $settings->analytics = Spyc::YAMLLoad($this->configdir . 'google_analytics.settings.yml');
    $settings->log = Spyc::YAMLLoad($this->configdir . 'system.logging.yml');
    $settings->tfa = Spyc::YAMLLoad($this->configdir . 'tfa.settings.yml');
    $settings->dateformat = Spyc::YAMLLoad($this->configdir . 'core.date_format.standard.yml');
    $settings->system = Spyc::YAMLLoad($this->configdir . 'system.site.yml');

    return $settings;
  }

  /**
   * @return array
   */
  public function getWebforms() {

    $webforms = glob($this->configdir . "webform.webform.*.yml");
    $wfs = [];
    foreach ($webforms as $key => $wf) {
      $w = Spyc::YAMLLoad($wf);
      $wfs[$key]['title'] = $w['title'];
      if (isset($w['handlers']['email'])) {
        $wfs[$key]['email'] = $w['handlers']['email']['settings']['to_mail'];
      }
      else {
        $wfs[$key]['email'] = "No email handler configured.";
      }

    }

    return $wfs;


  }

  /**
   * @return array
   */
  public function getExtensions() {
    $extensions = Spyc::YAMLLoad($this->configdir . 'core.extension.yml');
    $keymodules = ['devel', 'syslog'];
    $key_extensions = [];

    foreach ($extensions['module'] as $key => $e) {
      if (in_array($key, $keymodules)) {
        $key_extensions[] = $key;
      }
    }

    return $key_extensions;
  }

  /**
   * @return array
   */
  public function getViews() {

    $ignoreviews = [
      'Archive',
      'Custom block library',
      'Content',
      'Recent content',
      'Search',
      'Files',
      'Frontpage',
      'GovCMS Media Entity Browser',
      'Media',
      'Media Entity Browser',
      'Media List Reference',
      'Missing or bad alt text',
      'Moderated content',
      'Node List Reference',
      'Redirect',
      'Taxonomy term',
      'People',
      'Watchdog',
      'Webform submissions',
      'Who\'s new',
      'Who\'s online block',
      '',
      '',
      '',
      '',
      '',
    ];


    $viewslist = glob($this->configdir . "views.view.*.yml");
    $views = [];
    foreach ($viewslist as $key => $vl) {
      $vr = Spyc::YAMLLoad($vl);
      if (!in_array($vr['label'], $ignoreviews)) {
        $views[] = $vr;
      }
    }

    return $views;
  }

  /**
   * @return array
   */
  public function getParagraphs(){
    $paragraphs = glob($this->configdir . "paragraphs.paragraphs_type.*.yml");

    $paralist = [];
    $pars= [];
    foreach ($paragraphs as $key => $p) {

      $par = Spyc::YAMLLoad($p);
      $pattern = $par['id'];

      $pars[$key] = [
        'label' => $par['label'],
        'description' => $par['description'],
        'id' => $par['id'],
      ];
      foreach ($this->allfiles as $af) {
        if (strpos($af, 'field.field.paragraph.' . $pattern . '.') !== FALSE && strpos($af, 'field.field') !== FALSE) {
          $field = Spyc::YAMLLoad($this->configdir . $af);

          $pars[$key]["fields"][] = [
            'label' => $field['label'],
            'description' => $field['description'],
            'field_type' => $field['field_type'],
            'field_name' => $field['field_name'],
            'id' => $field['id'],
          ];
        }
      }
    }

    return $pars;
  }

  /**
   * @return array
   */
  public function getTaxonomies(){
    $tax = glob($this->configdir . "taxonomy.vocabulary.*.yml");

    $taxonomies = [];

    foreach ($tax as $key => $t) {
      $tx = Spyc::YAMLLoad($t);
      $taxonomies[] = [
        'name' => $tx['name'],
        'vid' => $tx['vid'],
      ];
    }

    return $taxonomies;
  }

  /**
   * @return array
   */
  public function getContentTypes(){
    $field_exceptions = [
      'field_components',
      'field_read_speaker',
      'field_search_exclude',
      'field_toc',
      'layout_builder__layout',
      'field_header_colour_classes',
      'field_header_modifiers',
      'list_string',
      'field_meta_tags',
      'field_keywords',
      'field_hide',
      'field_hero_darkness',
      '',
    ];
    $cts = glob($this->configdir . "node.type.*.yml");
    $this->allfiles = scandir($this->configdir);
    $typelist = [];
    foreach ($cts as $key => $f) {
      $typelist[$f]['fields'] = [];
      $typelist[$f]['data'] = Spyc::YAMLLoad($f);
      $exp = explode('.', $f);
      end($exp);
      $pattern = prev($exp);

      foreach ($this->allfiles as $af) {
        if (strpos($af, '.' . $pattern . '.') !== FALSE && strpos($af, 'field.field') !== FALSE) {
          $typelist[$f]['fields'][$af] = Spyc::YAMLLoad($this->configdir . $af);
        }
      }
    }

    return $typelist;
  }


}
