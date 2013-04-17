<?php

namespace classes;

/**
 * A class with static utility methods which publish notifications over the redis server
 * and are caught and handled by the node js server. Notifcations include: automated emails,
 * and status changes for candidates 
 */
class PredisHandler {

    private static $predis;
    /**
     * adds today's candidates as key/value store in the redis db. Keys are set to 
     * expire at midnight
     * @param type $candidates an array of candidates
     */
    public static function setCandidates($candidates) {

        if (is_array($candidates) && count($candidates > 0)) {
            $date = date('Y-m-d');
            $predis = self::$predis;
            //$predis = Registry::getItem('redis');

            $key = $predis->get("fetch_date:$date");
            if (!is_null($key)) {
                return;
            }
            $predis->set("fetch_date:$date", 1);
            // expire the hashes at midnight
            $TTL = strtotime('tomorrow') - time();
            $predis->expire("fetch_date:$date", $TTL);
            foreach ($candidates as $value) {
                $predis->hmset("user:$date:" . $value['pmpid'], 'fullname', $value['firstname'] . ' ' . $value['lastname'], 'check-in', 'false', 'PIA-start', 'false', 'IAQ-end', 'false');
                $predis->expire("user:$date:" . $value['pmpid'], $TTL);
                //    $predis->publish('CMS-mailer',$value['firstname'] . ' ' .$value['lastname']);
            }
        }
    }

    /**
     * Setter for the redis object which is injected from main.php
     * @param type $predis 
     */
    public static function setPredis($predis) {
        self::$predis = $predis;
    }

    /**
     * publishes a status update command on a redis channel. The node js server listens
     * on this channel and notifies connected sockets of the update
     * @param type $id pmpid of the candidate that's updated
     */
    public static function notifyStatusUpdate($id) {
        self::$predis->publish('statusUpdates', $id);
    }
    /**
     * publishes email message. The nodejs server takes care of sending the email.
     * @param type $body the email message
     * @param type $subject subject line
     * @param type $pmpid pmpid of associated candidate
     * @param type $email_type i.e. check-in, PIA-Start, IAQ-End
     * @param type $addresses array of recipient email addresses
     */
    //PredisHandler::setEmailBody($msg, $subject, $pmpid, $msg_type, $addresses);
    public static function setEmailBody($body, $subject, $pmpid, $email_type, $addresses) {
        
//        print_r($addresses);
//        die();
        $date = date('Y-m-d');
        $predis = self::$predis;
        
        // check redis to make sure this type of email hasn't already been sent for the current
        // candidate. spam protection.
        $mail_sent = $predis->hget("user:$date:$pmpid", $email_type);

        $predis->publish('addresses', json_encode($addresses));

        if ($mail_sent === "false") {

//                        $predis->hset("user:$date:$pmpid", 'inter_addreses', json_encode($addresses));
//                $predis->set("mail:$email_type:$pmpid", $body);
            $TTL = strtotime('tomorrow') - time();
            $predis->hset("user:$date:$pmpid", $email_type, 'true');
            $predis->expire("user:$date:$pmpid", $TTL);

//                $predis->expire("mail:$email_type:$pmpid", $TTL);
            $obj = array('message' => $body,
                'subject' => $subject,
                'type' => $email_type,
                'addresses' => $addresses,
                'pmpid' => $pmpid);

            $predis->publish('CMS-mailer', json_encode($obj));
            
            $timestamp = date('H:i:s');
            //$predis->push('loglist', "Mailer Event: $timestamp for user: $pmpid for email type: $email_type", false);
            
            
        }
    }

}

