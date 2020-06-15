<?php

class CRM_Event_Page_ParticipantListing_PicumRegion extends CRM_Event_Page_ParticipantListing_PicumBase {

  public function preProcess() {
    $this->_participantListingType = ['Region'];

    parent::preProcess();
  }

}
