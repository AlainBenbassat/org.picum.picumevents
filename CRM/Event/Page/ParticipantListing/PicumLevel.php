<?php

class CRM_Event_Page_ParticipantListing_PicumLevel extends CRM_Event_Page_ParticipantListing_PicumBase {

  public function preProcess() {
    $this->_participantListingType = ['Level'];

    parent::preProcess();
  }

}
