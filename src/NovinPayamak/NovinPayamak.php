<?php
/**
 * This library is to send SMS by NovinPayamak API
 *
 * @copyright   Copyright 2015 Saman Systems - NovinPayamak (http://novinpayamak.com/)
 */
namespace NovinPayamak;

use SoapClient;
use SoapFault;

class NovinPayamak {
    private static $webServiceUrl = 'http://novinpayamak.com/services/SMSBox/wsdl';
    private static $CISGatewebServiceUrl = 'http://novinpayamak.com/services/CISGate/wsdl';
    public static $gatewayNumber = '';
    public static $gatewayPassword = '';
    public static $panelEmail = '';
    public static $panelPassword = '';
    public static $messageId = 0;
    public static $panelRealCredit = 0;
    public static $gatewayCredit = 0;
    public static $recipients;
    public static $classifiedRecipients = [];
    public static $message;
    public static $flash = 0;

    // send SMS
    public static function sendMessage()
    {
        try {
            $client = new SoapClient(self::$webServiceUrl, ['encoding' => 'UTF-8', 'connection_timeout' => 5]);
            $response = $client->Send(
                [
                    'Auth' => ['number' => self::$gatewayNumber, 'pass' => self::$gatewayPassword],
                    'Recipients' => self::$recipients,
                    'ClassifiedRecipients' => self::$classifiedRecipients,
                    'Message' => self::$message,
                    'Flash' => self::$flash,
                ]
            );
            if ($response->Status == 1000) {
                self::$messageId = $response->MessageId;
                return true;
            }
        } catch(SoapFault $e) {
            // you can set log
        }
        return false;
    }

    // check remained count of SMS on the gateway
    public static function checkCredit()
    {
        try {
            $client = new SoapClient(self::$webServiceUrl, ['encoding' => 'UTF-8', 'connection_timeout' => 5]);
            $response = $client->CheckCredit(['Auth' => ['number' => self::$gatewayNumber, 'pass' => self::$gatewayPassword]]);
            if ($response->Status == 1000) {
                self::$gatewayCredit = $response->Credit;
                return true;
            }
        } catch(SoapFault $e) {
            // you can set log
        }
        return false;
    }

    // check panel credit, unit is Tooman
    public static function checkRealCredit()
    {
        try {
            $client = new SoapClient(self::$CISGatewebServiceUrl, ['encoding' => 'UTF-8', 'connection_timeout' => 5]);
            $response = $client->CheckRealCredit(['Auth' => ['email' => self::$panelEmail, 'password' => self::$panelPassword]]);
            if ($response->Status == 1000) {
                self::$panelRealCredit = $response->Credit;
                return true;
            }
        } catch(SoapFault $e) {
            // you can set log
        }
        return false;
    }
}
