<?php

class CRM_Event_Page_ParticipantListing_PicumRegionLevel extends CRM_Event_Page_ParticipantListing_PicumBase {

  public function preProcess() {
    $this->_participantListingType = ['Region', 'Level'];

    parent::preProcess();
  }

}
