<?php

/*
 * this is a copy of CRM/Event/Page/ParticipantListing/Simple.php
 */
class CRM_Event_Page_ParticipantListing_PicumBase extends CRM_Core_Page {

  protected $_id;

  protected $_participantListingType;

  protected $_eventTitle;

  protected $_pager;

  protected $_regions;

  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Integer', $this, TRUE);

    // retrieve Event Title and include it in page title
    $this->_eventTitle = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event',
      $this->_id,
      'title'
    );
    CRM_Utils_System::setTitle(ts('%1 - Participants', [1 => $this->_eventTitle]));

    // retrieve the regions
    $result = civicrm_api3('OptionValue', 'get', [
      'sequential' => 1,
      'option_group_id' => "region_20190625110618",
    ]);
    foreach ($result['values'] as $region) {
      $this->_regions[$region['value']] = $region['label'];
    }

    // we do not want to display recently viewed contacts since this is potentially a public page
    $this->assign('displayRecent', FALSE);
  }

  /**
   * @return string
   */
  public function run() {
    $this->preProcess();

    $fromClause = "
FROM       civicrm_contact
INNER JOIN civicrm_participant ON ( civicrm_contact.id = civicrm_participant.contact_id
           AND civicrm_contact.is_deleted = 0 )
INNER JOIN civicrm_event       ON civicrm_participant.event_id = civicrm_event.id
LEFT JOIN  civicrm_email       ON ( civicrm_contact.id = civicrm_email.contact_id AND civicrm_email.is_primary = 1 )
LEFT OUTER JOIN civicrm_value_geographical_area_1 g on g.entity_id = civicrm_contact.id
LEFT OUTER JOIN civicrm_country ctry on  ctry.id = g.country_of_representation_1
LEFT OUTER JOIN civicrm_option_value lev on lev.value = g.level_8 and lev.option_group_id = 94 
";

    $whereClause = "
WHERE    civicrm_event.id = %1
AND      civicrm_participant.is_test = 0
AND      civicrm_participant.status_id IN ( 1, 2 )";
    $params = [1 => [$this->_id, 'Integer']];
    $this->pager($fromClause, $whereClause, $params);
    $orderBy = $this->orderBy();

    list($offset, $rowCount) = $this->_pager->getOffsetAndRowCount();

    $query = "
SELECT   civicrm_contact.id           as contact_id    ,
         civicrm_contact.display_name as name          ,
         civicrm_contact.sort_name    as sort_name     ,
         civicrm_participant.id       as participant_id,
         civicrm_email.email          as email,
         civicrm_contact.organization_name,
         ctry.name as country,
         lev.label as level,
         g.region_45 as region
         $fromClause
         $whereClause
ORDER BY $orderBy
LIMIT    $offset, $rowCount";

    $rows = [];
    $object = CRM_Core_DAO::executeQuery($query, $params);
    while ($object->fetch()) {
      $row = [
        'country' => $object->country,
        'name' => $object->name,
        'organization' => $object->organization_name,
      ];

      if (in_array('Region', $this->_participantListingType)) {
        $r = explode(CRM_Core_DAO::VALUE_SEPARATOR, $object->region);
        $rgs = [];
        foreach ($r as $v) {
          if (array_key_exists($v, $this->_regions)) {
            $rgs[] = $this->_regions[$v];
          }
        }
        $row['region'] = implode(', ', $rgs);
      }
      if (in_array('Level', $this->_participantListingType)) {
        $row['level'] = $object->level;
      }

      $rows[] = $row;
    }
    $this->assign_by_ref('rows', $rows);

    return parent::run();
  }

  /**
   * @param $fromClause
   * @param $whereClause
   * @param array $whereParams
   */
  public function pager($fromClause, $whereClause, $whereParams) {

    $params = [];

    $params['status'] = ts('Group') . ' %%StatusMessage%%';
    $params['csvString'] = NULL;
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
    $params['rowCount'] = $this->get(CRM_Utils_Pager::PAGE_ROWCOUNT);
    if (!$params['rowCount']) {
      $params['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
    }

    $query = "
SELECT count( civicrm_contact.id )
       $fromClause
       $whereClause
";

    $params['total'] = CRM_Core_DAO::singleValueQuery($query, $whereParams);
    $this->_pager = new CRM_Utils_Pager($params);
    $this->assign_by_ref('pager', $this->_pager);
  }

  /**
   * @return string
   */
  public function orderBy() {
    static $headers = NULL;
    if (!$headers) {
      $headers = [];
      $i = 1;
      $headers[$i] = [
        'name' => ts('Country'),
        'sort' => 'country',
        'direction' => CRM_Utils_Sort::ASCENDING,
      ];
      $i++;
      $headers[$i] = [
        'name' => ts('Name'),
        'sort' => 'civicrm_contact.sort_name',
        'direction' => CRM_Utils_Sort::ASCENDING,
      ];
      $i++;
      $headers[$i] = [
        'name' => ts('Organization'),
        'sort' => 'civicrm_contact.organization_name',
        'direction' => CRM_Utils_Sort::DONTCARE,
      ];
      $i++;
      if (in_array('Region', $this->_participantListingType)) {
        $headers[$i] = [
          'name' => ts('Region'),
          'sort' => 'region',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ];
        $i++;
      }
      if (in_array('Level', $this->_participantListingType)) {
        $headers[$i] = [
          'name' => ts('Level'),
          'sort' => 'level',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ];
        $i++;
      }
    }
    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
      $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }
    $sort = new CRM_Utils_Sort($headers, $sortID);
    $this->assign_by_ref('headers', $headers);
    $this->assign_by_ref('sort', $sort);
    $this->set(CRM_Utils_Sort::SORT_ID,
      $sort->getCurrentSortID()
    );
    $this->set(CRM_Utils_Sort::SORT_DIRECTION,
      $sort->getCurrentSortDirection()
    );

    return $sort->orderBy();
  }

}
