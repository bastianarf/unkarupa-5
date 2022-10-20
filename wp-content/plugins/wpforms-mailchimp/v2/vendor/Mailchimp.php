<?php

require_once 'Mailchimp/Folders.php';
require_once 'Mailchimp/Templates.php';
require_once 'Mailchimp/Users.php';
require_once 'Mailchimp/Helper.php';
require_once 'Mailchimp/Mobile.php';
require_once 'Mailchimp/Conversations.php';
require_once 'Mailchimp/Ecomm.php';
require_once 'Mailchimp/Neapolitan.php';
require_once 'Mailchimp/Lists.php';
require_once 'Mailchimp/Campaigns.php';
require_once 'Mailchimp/Vip.php';
require_once 'Mailchimp/Reports.php';
require_once 'Mailchimp/Gallery.php';
require_once 'Mailchimp/Goal.php';
require_once 'Mailchimp/Exceptions.php';

class Mailchimp_WPF {
    
    public $apikey;
    public $ch;
    public $root  = 'https://api.mailchimp.com/2.0';
    public $debug = false;

    public static $error_map = array(
        "ValidationError" => "Mailchimp_ValidationError_WPF",
        "ServerError_MethodUnknown" => "Mailchimp_ServerError_MethodUnknown_WPF",
        "ServerError_InvalidParameters" => "Mailchimp_ServerError_InvalidParameters_WPF",
        "Unknown_Exception" => "Mailchimp_Unknown_Exception_WPF",
        "Request_TimedOut" => "Mailchimp_Request_TimedOut_WPF",
        "Zend_Uri_Exception" => "Mailchimp_Zend_Uri_Exception_WPF",
        "PDOException" => "Mailchimp_PDOException_WPF",
        "Avesta_Db_Exception" => "Mailchimp_Avesta_Db_Exception_WPF",
        "XML_RPC2_Exception" => "Mailchimp_XML_RPC2_Exception_WPF",
        "XML_RPC2_FaultException" => "Mailchimp_XML_RPC2_FaultException_WPF",
        "Too_Many_Connections" => "Mailchimp_Too_Many_Connections_WPF",
        "Parse_Exception" => "Mailchimp_Parse_Exception_WPF",
        "User_Unknown" => "Mailchimp_User_Unknown_WPF",
        "User_Disabled" => "Mailchimp_User_Disabled_WPF",
        "User_DoesNotExist" => "Mailchimp_User_DoesNotExist_WPF",
        "User_NotApproved" => "Mailchimp_User_NotApproved_WPF",
        "Invalid_ApiKey" => "Mailchimp_Invalid_ApiKey_WPF",
        "User_UnderMaintenance" => "Mailchimp_User_UnderMaintenance_WPF",
        "Invalid_AppKey" => "Mailchimp_Invalid_AppKey_WPF",
        "Invalid_IP" => "Mailchimp_Invalid_IP_WPF",
        "User_DoesExist" => "Mailchimp_User_DoesExist_WPF",
        "User_InvalidRole" => "Mailchimp_User_InvalidRole_WPF",
        "User_InvalidAction" => "Mailchimp_User_InvalidAction_WPF",
        "User_MissingEmail" => "Mailchimp_User_MissingEmail_WPF",
        "User_CannotSendCampaign" => "Mailchimp_User_CannotSendCampaign_WPF",
        "User_MissingModuleOutbox" => "Mailchimp_User_MissingModuleOutbox_WPF",
        "User_ModuleAlreadyPurchased" => "Mailchimp_User_ModuleAlreadyPurchased_WPF",
        "User_ModuleNotPurchased" => "Mailchimp_User_ModuleNotPurchased_WPF",
        "User_NotEnoughCredit" => "Mailchimp_User_NotEnoughCredit_WPF",
        "MC_InvalidPayment" => "Mailchimp_MC_InvalidPayment_WPF",
        "List_DoesNotExist" => "Mailchimp_List_DoesNotExist_WPF",
        "List_InvalidInterestFieldType" => "Mailchimp_List_InvalidInterestFieldType_WPF",
        "List_InvalidOption" => "Mailchimp_List_InvalidOption_WPF",
        "List_InvalidUnsubMember" => "Mailchimp_List_InvalidUnsubMember_WPF",
        "List_InvalidBounceMember" => "Mailchimp_List_InvalidBounceMember_WPF",
        "List_AlreadySubscribed" => "Mailchimp_List_AlreadySubscribed_WPF",
        "List_NotSubscribed" => "Mailchimp_List_NotSubscribed_WPF",
        "List_InvalidImport" => "Mailchimp_List_InvalidImport_WPF",
        "MC_PastedList_Duplicate" => "Mailchimp_MC_PastedList_Duplicate_WPF",
        "MC_PastedList_InvalidImport" => "Mailchimp_MC_PastedList_InvalidImport_WPF",
        "Email_AlreadySubscribed" => "Mailchimp_Email_AlreadySubscribed_WPF",
        "Email_AlreadyUnsubscribed" => "Mailchimp_Email_AlreadyUnsubscribed_WPF",
        "Email_NotExists" => "Mailchimp_Email_NotExists_WPF",
        "Email_NotSubscribed" => "Mailchimp_Email_NotSubscribed_WPF",
        "List_MergeFieldRequired" => "Mailchimp_List_MergeFieldRequired_WPF",
        "List_CannotRemoveEmailMerge" => "Mailchimp_List_CannotRemoveEmailMerge_WPF",
        "List_Merge_InvalidMergeID" => "Mailchimp_List_Merge_InvalidMergeID_WPF",
        "List_TooManyMergeFields" => "Mailchimp_List_TooManyMergeFields_WPF",
        "List_InvalidMergeField" => "Mailchimp_List_InvalidMergeField_WPF",
        "List_InvalidInterestGroup" => "Mailchimp_List_InvalidInterestGroup_WPF",
        "List_TooManyInterestGroups" => "Mailchimp_List_TooManyInterestGroups_WPF",
        "Campaign_DoesNotExist" => "Mailchimp_Campaign_DoesNotExist_WPF",
        "Campaign_StatsNotAvailable" => "Mailchimp_Campaign_StatsNotAvailable_WPF",
        "Campaign_InvalidAbsplit" => "Mailchimp_Campaign_InvalidAbsplit_WPF",
        "Campaign_InvalidContent" => "Mailchimp_Campaign_InvalidContent_WPF",
        "Campaign_InvalidOption" => "Mailchimp_Campaign_InvalidOption_WPF",
        "Campaign_InvalidStatus" => "Mailchimp_Campaign_InvalidStatus_WPF",
        "Campaign_NotSaved" => "Mailchimp_Campaign_NotSaved_WPF",
        "Campaign_InvalidSegment" => "Mailchimp_Campaign_InvalidSegment_WPF",
        "Campaign_InvalidRss" => "Mailchimp_Campaign_InvalidRss_WPF",
        "Campaign_InvalidAuto" => "Mailchimp_Campaign_InvalidAuto_WPF",
        "MC_ContentImport_InvalidArchive" => "Mailchimp_MC_ContentImport_InvalidArchive_WPF",
        "Campaign_BounceMissing" => "Mailchimp_Campaign_BounceMissing_WPF",
        "Campaign_InvalidTemplate" => "Mailchimp_Campaign_InvalidTemplate_WPF",
        "Invalid_EcommOrder" => "Mailchimp_Invalid_EcommOrder_WPF",
        "Absplit_UnknownError" => "Mailchimp_Absplit_UnknownError_WPF",
        "Absplit_UnknownSplitTest" => "Mailchimp_Absplit_UnknownSplitTest_WPF",
        "Absplit_UnknownTestType" => "Mailchimp_Absplit_UnknownTestType_WPF",
        "Absplit_UnknownWaitUnit" => "Mailchimp_Absplit_UnknownWaitUnit_WPF",
        "Absplit_UnknownWinnerType" => "Mailchimp_Absplit_UnknownWinnerType_WPF",
        "Absplit_WinnerNotSelected" => "Mailchimp_Absplit_WinnerNotSelected_WPF",
        "Invalid_Analytics" => "Mailchimp_Invalid_Analytics_WPF",
        "Invalid_DateTime" => "Mailchimp_Invalid_DateTime_WPF",
        "Invalid_Email" => "Mailchimp_Invalid_Email_WPF",
        "Invalid_SendType" => "Mailchimp_Invalid_SendType_WPF",
        "Invalid_Template" => "Mailchimp_Invalid_Template_WPF",
        "Invalid_TrackingOptions" => "Mailchimp_Invalid_TrackingOptions_WPF",
        "Invalid_Options" => "Mailchimp_Invalid_Options_WPF",
        "Invalid_Folder" => "Mailchimp_Invalid_Folder_WPF",
        "Invalid_URL" => "Mailchimp_Invalid_URL_WPF",
        "Module_Unknown" => "Mailchimp_Module_Unknown_WPF",
        "MonthlyPlan_Unknown" => "Mailchimp_MonthlyPlan_Unknown_WPF",
        "Order_TypeUnknown" => "Mailchimp_Order_TypeUnknown_WPF",
        "Invalid_PagingLimit" => "Mailchimp_Invalid_PagingLimit_WPF",
        "Invalid_PagingStart" => "Mailchimp_Invalid_PagingStart_WPF",
        "Max_Size_Reached" => "Mailchimp_Max_Size_Reached_WPF",
        "MC_SearchException" => "Mailchimp_MC_SearchException_WPF",
        "Goal_SaveFailed" => "Mailchimp_Goal_SaveFailed_WPF",
        "Conversation_DoesNotExist" => "Mailchimp_Conversation_DoesNotExist_WPF",
        "Conversation_ReplySaveFailed" => "Mailchimp_Conversation_ReplySaveFailed_WPF",
        "File_Not_Found_Exception" => "Mailchimp_File_Not_Found_Exception_WPF",
        "Folder_Not_Found_Exception" => "Mailchimp_Folder_Not_Found_Exception_WPF",
        "Folder_Exists_Exception" => "Mailchimp_Folder_Exists_Exception_WPF"
    );

    public function __construct($apikey=null, $opts=array()) {
        if (!$apikey) {
            $apikey = getenv('MAILCHIMP_APIKEY');
        }

        if (!$apikey) {
            $apikey = $this->readConfigs();
        }

        if (!$apikey) {
            throw new Mailchimp_Error_WPF('You must provide a MailChimp API key');
        }

        $this->apikey = $apikey;
        $dc           = "us1";

        if (strstr($this->apikey, "-")){
            list($key, $dc) = explode("-", $this->apikey, 2);
            if (!$dc) {
                $dc = "us1";
            }
        }

        $this->root = str_replace('https://api', 'https://' . $dc . '.api', $this->root);
        $this->root = rtrim($this->root, '/') . '/';

        if (!isset($opts['timeout']) || !is_int($opts['timeout'])){
            $opts['timeout'] = 600;
        }
        if (isset($opts['debug'])){
            $this->debug = true;
        }


        $this->ch = curl_init();

        if (isset($opts['CURLOPT_FOLLOWLOCATION']) && $opts['CURLOPT_FOLLOWLOCATION'] === true) {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);    
        }

        curl_setopt($this->ch, CURLOPT_USERAGENT, 'MailChimp-PHP/2.0.6');
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $opts['timeout']);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);


        $this->folders = new Mailchimp_Folders_WPF($this);
        $this->templates = new Mailchimp_Templates_WPF($this);
        $this->users = new Mailchimp_Users_WPF($this);
        $this->helper = new Mailchimp_Helper_WPF($this);
        $this->mobile = new Mailchimp_Mobile_WPF($this);
        $this->conversations = new Mailchimp_Conversations_WPF($this);
        $this->ecomm = new Mailchimp_Ecomm_WPF($this);
        $this->neapolitan = new Mailchimp_Neapolitan_WPF($this);
        $this->lists = new Mailchimp_Lists_WPF($this);
        $this->campaigns = new Mailchimp_Campaigns_WPF($this);
        $this->vip = new Mailchimp_Vip_WPF($this);
        $this->reports = new Mailchimp_Reports_WPF($this);
        $this->gallery = new Mailchimp_Gallery_WPF($this);
        $this->goal = new Mailchimp_Goal_WPF($this);
    }

    public function __destruct() {
        if(is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

    public function call($url, $params) {
        $params['apikey'] = $this->apikey;
        
        $params = json_encode($params);
        $ch     = $this->ch;

        curl_setopt($ch, CURLOPT_URL, $this->root . $url . '.json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);

        $start = microtime(true);
        $this->log('Call to ' . $this->root . $url . '.json: ' . $params);
        if($this->debug) {
            $curl_buffer = fopen('php://memory', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
        }

        $response_body = curl_exec($ch);

        $info = curl_getinfo($ch);
        $time = microtime(true) - $start;
        if($this->debug) {
            rewind($curl_buffer);
            $this->log(stream_get_contents($curl_buffer));
            fclose($curl_buffer);
        }
        $this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
        $this->log('Got response: ' . $response_body);

        if(curl_error($ch)) {
            throw new Mailchimp_HttpError_WPF("API call to $url failed: " . curl_error($ch));
        }
        $result = json_decode($response_body, true);
        
        if(floor($info['http_code'] / 100) >= 4) {
            throw $this->castError($result);
        }

        return $result;
    }

    public function readConfigs() {
        $paths = array('~/.mailchimp.key', '/etc/mailchimp.key');
        foreach($paths as $path) {
            if(file_exists($path)) {
                $apikey = trim(file_get_contents($path));
                if ($apikey) {
                    return $apikey;
                }
            }
        }
        return false;
    }

    public function castError($result) {
        if ($result['status'] !== 'error' || !$result['name']) {
            throw new Mailchimp_Error_WPF('We received an unexpected error: ' . json_encode($result));
        }

        $class = (isset(self::$error_map[$result['name']])) ? self::$error_map[$result['name']] : 'Mailchimp_Error_WPF';
        return new $class($result['error'], $result['code']);
    }

    public function log($msg) {
        if ($this->debug) {
            error_log($msg);
        }
    }
}


