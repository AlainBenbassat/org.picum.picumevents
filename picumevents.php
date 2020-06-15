<?php

require_once 'picumevents.civix.php';
use CRM_Picumevents_ExtensionUtil as E;

function picumevents_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  // check if it's the public participant list (form name starts with CRM_Event_Page_ParticipantListing_)
  if (strpos($formName, 'CRM_Event_Page_ParticipantListing_') !== FALSE) {
    $showParticipantList = FALSE;

    // check if the user is logged in
    $contactID = CRM_Core_Session::getLoggedInContactID();
    if ($contactID) {
      $showParticipantList = TRUE;
    }
    else {
      // see if we have a contact id and checksum in the url
      $contactID = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject, FALSE);
      $checksum = CRM_Utils_Request::retrieve('cs', 'String', CRM_Core_DAO::$_nullObject, FALSE);

      // validate contact id and checksum
      $isValidUser = CRM_Contact_BAO_Contact_Utils::validChecksum($contactID, $checksum);
      if ($isValidUser) {
        $showParticipantList = TRUE;
      }
    }

    if ($showParticipantList == FALSE) {
      // user is not allowed to see the participant list: show another template
      $tplName = 'CRM/Event/Page/ParticipantListing/NoAccess.tpl';
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function picumevents_civicrm_config(&$config) {
  _picumevents_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function picumevents_civicrm_xmlMenu(&$files) {
  _picumevents_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function picumevents_civicrm_install() {
  _picumevents_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function picumevents_civicrm_postInstall() {
  _picumevents_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function picumevents_civicrm_uninstall() {
  _picumevents_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function picumevents_civicrm_enable() {
  _picumevents_civix_civicrm_enable();

  // get the existing ones
  $params = [
    'sequential' => 1,
    'option_group_id' => "participant_listing",
  ];
  $participantListings = civicrm_api3('OptionValue', 'get', $params);

  // the PICUM participant listings
  $extraListings = [
    'CRM_Event_Page_ParticipantListing_PicumBase' => 'PICUM: Country, Name, Organization',
    'CRM_Event_Page_ParticipantListing_PicumRegion' => 'PICUM: Country, Name, Organization, Region',
    'CRM_Event_Page_ParticipantListing_PicumLevel' => 'PICUM: Country, Name, Organization, Level',
    'CRM_Event_Page_ParticipantListing_PicumRegionLevel' => 'PICUM: Country, Name, Organization, Region, Level',
  ];

  // Due to bug in civi you must remove NOT NULL from civicrm_option_value label columns (label_en_US, label_fr_FR, label_es_ES...)
  $maxValue = CRM_Core_DAO::singleValueQuery("select max(value) from civicrm_option_value where option_group_id = 29");
  if ($maxValue == 3) {
    foreach ($extraListings as $extraListingClass => $extraListingLabel) {
      $params = [
        'option_group_id' => 29,
        'name' => $extraListingLabel,
        'label' => $extraListingLabel,
        'description' => $extraListingClass,
      ];
      civicrm_api3('OptionValue', 'create', $params);
    }
  }
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function picumevents_civicrm_disable() {
  _picumevents_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function picumevents_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _picumevents_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function picumevents_civicrm_managed(&$entities) {
  _picumevents_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function picumevents_civicrm_caseTypes(&$caseTypes) {
  _picumevents_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function picumevents_civicrm_angularModules(&$angularModules) {
  _picumevents_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function picumevents_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _picumevents_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function picumevents_civicrm_entityTypes(&$entityTypes) {
  _picumevents_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function picumevents_civicrm_themes(&$themes) {
  _picumevents_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function picumevents_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function picumevents_civicrm_navigationMenu(&$menu) {
  _picumevents_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _picumevents_civix_navigationMenu($menu);
} // */
