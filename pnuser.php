<?php

function Mailz_user_cron() 
{

    $pwd = (string) FormUtil::getPassedValue('pwd');
    $id  = (int)    FormUtil::getPassedValue('id');
    
    // Get newsletter
    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter) {
        print "NEWSLETTER INVALID";
    } else {
        if ($newsletter['croncode'] != $pwd) {
            print "CRON PWD WRONG";
        } else {
            if ($newsletter['croncode'] == '') {
                print "CRON FOR NEWSLETTER INACTIVE";
            } else {
                // Start sending
                $result = pnModAPIFunc('mailz','common','queueNewsletter',array('id' => $newsletter['id']));
                if ($result) {
                    print "NEWSLETTER QUEUED FOR SENDING!";
                } else {
                    print "ERROR QUEUING NEWSLETTER";
                }
            }
        }
    }
    return true;
}

