<?php

/**
 * @file
 * Add a table of notes from related contacts.
 *
 * Copyright (C) 2013-15, AGH Strategies, LLC <info@aghstrategies.com>
 * Licensed under the GNU Affero Public License 3.0 (see LICENSE.txt)
 */

require_once 'lateregistration.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function lateregistration_civicrm_config(&$config) {
  _lateregistration_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function lateregistration_civicrm_xmlMenu(&$files) {
  _lateregistration_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function lateregistration_civicrm_install() {
  return _lateregistration_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function lateregistration_civicrm_uninstall() {
  return _lateregistration_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function lateregistration_civicrm_enable() {
  return _lateregistration_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function lateregistration_civicrm_disable() {
  return _lateregistration_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function lateregistration_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _lateregistration_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function lateregistration_civicrm_managed(&$entities) {
  return _lateregistration_civix_civicrm_managed($entities);
}
/**
 * Implementation of hook_civicrm_navigationMenu
 */
function lateregistration_civicrm_navigationMenu(&$navMenu) {
  $pages = array(
    'settings_page' => array(
      'label'      => 'Late Registration Settings',
      'name'       => 'Late Registration Settings',
      'url'        => 'civicrm/admin/lateregistration',
      'parent'    => array('Administer', 'System Settings'),
      'permission' => 'access CiviCRM',
      'operator'   => NULL,
      'separator'  => NULL,
      'active'     => 1,
    ),
  );
  foreach ($pages as $item) {
    // Check that our item doesn't already exist.
    $menu_item_search = array('url' => $item['url']);
    $menu_items = array();
    CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);
    if (empty($menu_items)) {
      $path = implode('/', $item['parent']);
      unset($item['parent']);
      _lateregistration_civix_insert_navigation_menu($navMenu, $path, $item);
    }
  }
}
/**
 * Implements hook_civicrm_buildAmount().
 *
 * @param string $pageType
 * @param CRM_Core_Form $form
 */
function lateregistration_civicrm_buildAmount($pageType, &$form, &$amount) {
  if (!empty($form->get('mid'))) {
    return;
  }
  $priceSetId = $form->get('priceSetId');
  if (!empty($priceSetId)) {
    $feeBlock = &$amount;
    if (!is_array($feeBlock) || empty($feeBlock)) {
      return;
    }
    if ($pageType == 'event') {
      $events = $form->get('values');
      $event_start_date = $events['event']['event_start_date'];
      $today_date = date('Y-m-d');      
      foreach ($feeBlock as &$fee) {
        if (!is_array($fee['options'])) {
          continue;
        }
        $data = Civi::settings()->get('price_'.$fee['id']);
        if( !empty($data) ){
          $days_prior = $data['days_prior'];
          $days_ago = date('Y-m-d', strtotime("$event_start_date - $days_prior day"));
          if($days_ago <= $today_date){
            foreach ($fee['options'] as &$option) {
              if ($option['amount'] > 0) {
                $option['amount'] = $option['amount'] + $data['late_fees'];
              }
            }
          }
        }
      }
      $form->_priceSet['fields'] = $feeBlock;
    }
  }
}
