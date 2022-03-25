<?php

use CRM_Summmary_ExtensionUtil as E;


function civicrm_api3_get_birthday_user_get_send()
{
    $sql = "SELECT civicrm_contact.first_name, civicrm_email.email 
    FROM civicrm_contact 
    INNER JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id
    WHERE MONTH(birth_date) = MONTH(NOW()) AND DAY(birth_date) = DAY(NOW())
    AND do_not_email = FALSE;";
    $exec = CRM_Core_DAO::executeQuery($sql);

    while ($exec->fetch()) {
        $contacts[$exec->first_name] = $exec->email;
    }

    foreach ($contacts as $contact => $value) {
        $name = CRM_Core_Smarty::singleton();
        $name->assign('first_name', $contact);
        // var_dump($name);
        // die;
        $sendTemplateParams
            = [
                'from' => 'CiviCRM',
                'subject' => 'Happy birthday',
                'messageTemplateID' => 69,
                'toEmail' => $value,
            ];

        $sent = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);

        if (!$sent) {
            // echo '<pre>';
            CRM_Core_Session::setStatus(E::ts('Something went wrong'), 'error');

            // var_dump(CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams));
            // var_dump($sendTemplateParams);
            // die;
            // echo '</pre>';
        }
    }

    // $sent = CRM_Core_BAO_MessageTemplate::sendTemplate($sendTemplateParams);

    // var_dump($sendTemplateParams);
    // die;


    // var_dump($sent);
    // die;
    // var_dump($sendTemplateParams);
    // die;
    // var_dump($name);
}
