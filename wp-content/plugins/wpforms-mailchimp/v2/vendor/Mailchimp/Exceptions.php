<?php

class Mailchimp_Error_WPF extends Exception {}
class Mailchimp_HttpError_WPF extends Mailchimp_Error_WPF {}

/**
 * The parameters passed to the API call are invalid or not provided when required
 */
class Mailchimp_ValidationError_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_ServerError_MethodUnknown_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_ServerError_InvalidParameters_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Unknown_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Request_TimedOut_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Zend_Uri_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_PDOException_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Avesta_Db_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_XML_RPC2_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_XML_RPC2_FaultException_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Too_Many_Connections_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Parse_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_Unknown_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_Disabled_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_DoesNotExist_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_NotApproved_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_ApiKey_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_UnderMaintenance_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_AppKey_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_IP_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_DoesExist_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_InvalidRole_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_InvalidAction_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_MissingEmail_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_CannotSendCampaign_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_MissingModuleOutbox_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_ModuleAlreadyPurchased_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_ModuleNotPurchased_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_User_NotEnoughCredit_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_MC_InvalidPayment_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_DoesNotExist_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidInterestFieldType_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidOption_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidUnsubMember_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidBounceMember_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_AlreadySubscribed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_NotSubscribed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidImport_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_MC_PastedList_Duplicate_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_MC_PastedList_InvalidImport_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Email_AlreadySubscribed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Email_AlreadyUnsubscribed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Email_NotExists_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Email_NotSubscribed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_MergeFieldRequired_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_CannotRemoveEmailMerge_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_Merge_InvalidMergeID_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_TooManyMergeFields_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidMergeField_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_InvalidInterestGroup_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_List_TooManyInterestGroups_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_DoesNotExist_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_StatsNotAvailable_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidAbsplit_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidContent_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidOption_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidStatus_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_NotSaved_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidSegment_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidRss_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidAuto_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_MC_ContentImport_InvalidArchive_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_BounceMissing_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidTemplate_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_EcommOrder_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownError_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownSplitTest_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownTestType_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownWaitUnit_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownWinnerType_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Absplit_WinnerNotSelected_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_Analytics_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_DateTime_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_Email_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_SendType_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_Template_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_TrackingOptions_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_Options_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_Folder_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_URL_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Module_Unknown_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_MonthlyPlan_Unknown_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Order_TypeUnknown_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_PagingLimit_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Invalid_PagingStart_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Max_Size_Reached_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_MC_SearchException_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Goal_SaveFailed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Conversation_DoesNotExist_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Conversation_ReplySaveFailed_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_File_Not_Found_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Folder_Not_Found_Exception_WPF extends Mailchimp_Error_WPF {}

/**
 * None
 */
class Mailchimp_Folder_Exists_Exception_WPF extends Mailchimp_Error_WPF {}


