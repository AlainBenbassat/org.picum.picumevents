<?php
use CRM_Picumevents_ExtensionUtil as E;

class CRM_Picumevents_Page_PicumAllEvents extends CRM_Core_Page {

  public function run() {
    $year = CRM_Utils_Request::retrieveValue('year', 'Integer', date('Y'));
    $sort = CRM_Utils_Request::retrieveValue('sort', 'Integer', 3);
    $newsort = CRM_Utils_Request::retrieveValue('newsort', 'Integer', 0);
    $sortorder = CRM_Utils_Request::retrieveValue('sortorder', 'Integer', 0);

    CRM_Utils_System::setTitle("PICUM Events - $year");

    $invertedSortorder = ($sortorder == 0) ? 1 : 0;
    $currentURL = CRM_Utils_System::url('civicrm/picumallevents', "reset=1&year=$year&sort=$sort&sortorder=$invertedSortorder");
    $this->assign('currentURL', $currentURL);

    // see how we have to sort the result
    if ($sort == $newsort) {
      if ($sortorder == 0) {
        $sortorder = 'asc';
      }
      else {
        $sortorder = 'desc';
      }
    }
    else {
      $sortorder = 'asc';
    }
    if ($newsort > 0) {
      $sort = $newsort;
    }

    $events = $this->getAllEvents($year, $sort, $sortorder);
    $this->assign('events', $events);

    parent::run();
  }

  private function getAllEvents($year, $sort, $sortorder) {
    $sql = "
      select
        e.id,
        event_type.label event_type,
        date_format(start_date, '%Y-%m-%d') start_date,
        e.title event,
        ed.output_number_98 output_number,
        meeting_place.label meeting_place,
        count(distinct p_pos.id) num_participant_pos,
        count(distinct p_neg.id) num_participant_neg
      from
        civicrm_event e
      left outer join 
        civicrm_value_events_detail_9 ed on ed.entity_id = e.id
      left outer join
        civicrm_option_value event_type on e.event_type_id = event_type.value and event_type.option_group_id = 15
      left outer join
        civicrm_option_value meeting_place on ed.meeting_place_110 = meeting_place.value and meeting_place.option_group_id = 132
      left outer join
        civicrm_participant p_pos on p_pos.event_id = e.id and p_pos.status_id in (1, 2)
      left outer join
        civicrm_contact c_pos on p_pos.contact_id = c_pos.id     
      left outer join
        civicrm_participant p_neg on p_neg.event_id = e.id and p_neg.status_id not in (1, 2)
      left outer join
        civicrm_contact c_neg on p_neg.contact_id = c_neg.id                                     
      where
        year(e.start_date) = $year
      AND
        e.is_template = 0
      and
        ifnull(c_pos.is_deleted, 0) = 0
      and
        ifnull(c_neg.is_deleted, 0) = 0
      group by
        e.id,
        event_type.label,
        date_format(start_date, '%Y-%m-%d'),
        e.title,
        ed.output_number_98,
        meeting_place.label              
      order by
        $sort $sortorder    
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);
    return $dao->fetchAll();
  }

}
