<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2018                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2018
 */

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Relationship Type.
 */
class CRM_Admin_Form_Setting_lateregistration extends CRM_Admin_Form_Setting {
  
  /**
   * Set default values for the form.
   *
   * Default values are retrieved from the database.
   */
  public function setDefaultValues() {
    if (!$this->_defaults) {
      $this->_defaults = array();
      //$this->_defaults['late_fees'] = Civi::settings()->get('late_fees');
      //$this->_defaults['days_prior'] = Civi::settings()->get('days_prior');
    }
    return $this->_defaults;
  }
  

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Late Registration Settings'));
    $priceSet = civicrm_api3('PriceSet', 'get', array('extends'=>1));
    if( !empty($priceSet['values']) ){
      $priceSetID = array();
      foreach($priceSet['values'] as $key =>$value){
        $priceSet_ID = $value['id'];
        $priceSet_Name = $value['title'];
        $price_field = civicrm_api3('PriceField', 'get', array('price_set_id' => $priceSet_ID));
        if(!empty($price_field['values'])){
          foreach($price_field['values'] as $pricekey=>$pricevalue){
            $priceSetID[$pricekey] = $priceSet_Name.'::'.$pricevalue['label'];
          }
        }
      }
    }
    
    $this->add('select', 'price_fields', ts('Select Price Field to set late fees'), $priceSetID, TRUE, array('multiple' => 'multiple', 'class' => 'crm-select2', 'size' => 10, 'style' => 'width:300px'));
    $this->add('text', 'late_fees', ts('Late Registration fees'), '' );
    $this->add('text', 'days_prior', ts('Days prior to late registration'), '' );
    $this->addFormRule(array('CRM_Admin_Form_Setting_lateregistration', 'formRule'), $this);
    
    parent::buildQuickForm();
  }

  /**
   * Global form rule.
   *
   * @param array $fields
   *   The input form values.
   * @param array $files
   *   The uploaded files if any.
   * @param array $options
   *   Additional user data.
   *
   * @return bool|array
   *   true if no errors, else array of errors
   */
  public static function formRule($fields, $files, $options) {
    $errors = array();
    if (array_key_exists('late_fees', $fields) && !is_numeric($fields['late_fees'])) {
      $errors['late_fees'] = ts('Invalid field value. Only numeric value is allowed.');
    }
    if (array_key_exists('days_prior', $fields) && !is_numeric($fields['days_prior'])) {
      $errors['days_prior'] = ts('Invalid field value. Only integer value is allowed.');
    }
    return $errors;
  }
  
  /**
   * postProcess the form object.
   */
  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);
    
    $priceFieldsID = $params['price_fields'];
    $late_fees = $params['late_fees'];
    $days_prior = $params['days_prior'];
    $data = array();
    foreach($priceFieldsID as $id){
      $data = array('late_fees' => $late_fees, 'days_prior' => $days_prior);
      Civi::settings()->set('price_'.$id, $data);
    }
    CRM_Core_Config::clearDBCache();
    CRM_Utils_System::flushCache();
    CRM_Core_Resources::singleton()->resetCacheCode();
    CRM_Core_Session::setStatus(" ", ts('Changes Saved'), "success");
  }
}
