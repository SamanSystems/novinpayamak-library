<?php
/**
 * This library is to send SMS by NovinPayamak API
 *
 * @copyright   Copyright 2015 Saman Systems - NovinPayamak (http://novinpayamak.com/)
 */
namespace NovinPayamak;

class NovinPayamak {
    private static $webServiceUrl = 'http://novinpayamak.com/services/SMSBox/wsdl';
    private static $CISGatewebServiceUrl = 'http://novinpayamak.com/services/CISGate/wsdl';
    private static $gatewayNumber = '';
    private static $gatewayPassword = '';
    private static $panelEmail = '';
    private static $panelPassword = '';
    public static $SMSPanelRealCredit = 0;
    public static $recipients;
    public static $classifiedRecipients = [];
    public static $message;
    public static $flash = 0;

    // send SMS
    public static function send() {
        try {
            $client = new SoapClient(self::$webServiceUrl, ['encoding'=>'UTF-8', 'connection_timeout' => 5]);
            $res = $client->Send(
                [
                    'Auth' => ['number' => self::$gatewayNumber, 'pass' => self::$gatewayPassword],
                    'Recipients' => self::$recipients,
                    'ClassifiedRecipients' => self::$classifiedRecipients,
                    'Message' => self::$message,
                    'Flash' => self::$flash,
                ]
            );
            return $res;
        } catch(SoapFault $e){
            // you can set log
        }
        return [];
    }

    // check remained count of SMS on the gateway
    public static function CheckCredit(){
        try {
            $client = new SoapClient(self::$webServiceUrl, ['encoding'=>'UTF-8', 'connection_timeout' => 5]);
            $credit = $client->CheckCredit(['Auth' => ['number' => self::$gatewayNumber, 'pass' => self::$gatewayPassword]]);
            return $credit;
        } catch(SoapFault $e){
            // you can set log
        }
        return false;
    }

    // check panel credit, unit is Tooman
    public static function CheckRealCredit(){
        try {
            $client = new SoapClient(self::$CISGatewebServiceUrl, ['encoding'=>'UTF-8', 'connection_timeout' => 5]);
            $credit = $client->CheckRealCredit(['Auth' => ['email' => self::$panelEmail, 'password' => self::$panelPassword]]);
            if ($credit->Status == 1000) {
                self::$SMSPanelRealCredit = $credit->Credit;
                return true;
            }
        } catch(SoapFault $e) {
            // you can set log
        }
        return false;
    }
}