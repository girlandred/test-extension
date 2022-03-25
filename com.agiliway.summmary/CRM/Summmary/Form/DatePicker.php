<?php

use CRM_Summmary_ExtensionUtil as E;

class CRM_Summmary_Form_DatePicker extends CRM_Core_Form
{
  public function setDefaultValues()
  {
    $defaults = $this->_values;

    if (empty($defaults['end_date'])) {
      $defaults['end_date'] = date('Y-m-d');
    }
    return $defaults;
  }

  public function buildQuickForm()
  {
    $this->add(
      'datepicker',
      'start_date',
      ts('Start Date'),
      ['Y-m-d'],
      FALSE,
      ['time' => FALSE]
    );
    $this->add(
      'datepicker',
      'end_date',
      ts('End Date'),
      ['Y-m-d'],
      FALSE,
      ['time' => FALSE]
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    $entities = [
      'contribution' => 'receive_date',
      'contact' => 'created_date',
      'activity' => 'created_date',
      'event' => 'created_date',
      'membership' => 'start_date',
      'case' => 'created_date',
    ];

    $values = [];
    $result = [];

    foreach ($entities as $key => $entity) {
      $result[$key] = civicrm_api3(
        $key,
        'getcount',
        $values
      );
    }
    $this->assign('startResults', $result);

    parent::buildQuickForm();
  }

  public function addRules()
  {
    $this->addFormRule([CRM_Summmary_Form_DatePicker::class, 'formRule']);
  }

  public static function formRule($values)
  {
    if ($values['end_date'] < $values['start_date']) {
      $errors['end_date'] = E::ts('End date should be after Start date.');
    }
    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess()
  {
    $params = $this->exportValues();
    $entities = [
      'contribution' => 'receive_date',
      'contact' => 'created_date',
      'activity' => 'created_date',
      'event' => 'created_date',
      'membership' => 'start_date',
      'case' => 'created_date',
    ];

    $start_date = $params['start_date'];
    $end_date = $params['end_date'];
    $values = [];

    if (!empty($params['start_date'])) {
      $values = [
        'receive_date' => [
          '>=' => $start_date
        ],
        'created_date' => [
          '>=' => $start_date
        ],
        'start_date' => [
          '>=' => $start_date
        ]
      ];
    }


    if (!empty($params['end_date'])) {
      $values = [
        'receive_date' => [
          '<=' => $end_date
        ],
        'created_date' => [
          '<=' => $end_date
        ],
        'start_date' => [
          '<=' => $end_date
        ]
      ];
    }

    $result = [];
    foreach ($entities as $key => $entity) {
      $result[$key] = civicrm_api3(
        $key,
        'getcount',
        $values
      );
    }
    $this->assign('startResults', $result);

    $userID = CRM_Core_Session::getLoggedInContactID();
    $userMail = CRM_Contact_BAO_Contact_Location::getEmailDetails($userID);
    $sendTemplateParams
      = [
        'userID' => $userID,
        'tplParams' => $result,
        'from' => 'CiviCRM',
        'subject' => 'Report details',
        'messageTemplateID' => 68,
        'toEmail' => $userMail[1],
      ];

    $sent = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);
    if ($sent) {
      CRM_Core_Session::setStatus(E::ts('Report was sent'), 'success');
    } else {
      CRM_Core_Session::setStatus(E::ts('Something went wrong'), 'error');
    }
    parent::postProcess();
  }
}
