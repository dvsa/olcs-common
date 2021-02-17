<?php

namespace Common;

/**
 * Holds the Ref Data constants required by the web app
 */
class RefData
{
    const LICENCE_TYPE_RESTRICTED = 'ltyp_r';
    const LICENCE_TYPE_STANDARD_INTERNATIONAL = 'ltyp_si';
    const LICENCE_TYPE_STANDARD_NATIONAL = 'ltyp_sn';
    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';

    const CASE_TYPE_LICENCE = 'case_t_lic';
    const CASE_TYPE_APPLICATION = 'case_t_app';
    const CASE_TYPE_TM = 'case_t_tm';
    const CASE_TYPE_IMPOUNDING = 'case_t_imp';

    const FEE_PAYMENT_METHOD_CARD_ONLINE  = 'fpm_card_online';
    const FEE_PAYMENT_METHOD_CARD_OFFLINE = 'fpm_card_offline';
    const FEE_PAYMENT_METHOD_CASH         = 'fpm_cash';
    const FEE_PAYMENT_METHOD_CHEQUE       = 'fpm_cheque';
    const FEE_PAYMENT_METHOD_POSTAL_ORDER = 'fpm_po';
    const FEE_PAYMENT_METHOD_WAIVE        = 'fpm_waive';

    const FEE_STATUS_OUTSTANDING       = 'lfs_ot';
    const FEE_STATUS_PAID              = 'lfs_pd';
    const FEE_STATUS_CANCELLED         = 'lfs_cn';

    const FEE_TYPE_CONT = 'CONT';

    const TRANSACTION_STATUS_COMPLETE    = 'pay_s_pd';
    const TRANSACTION_STATUS_OUTSTANDING = 'pay_s_os';
    const TRANSACTION_STATUS_CANCELLED   = 'pay_s_cn';
    const TRANSACTION_STATUS_FAILED      = 'pay_s_fail';
    const TRANSACTION_STATUS_PAID        = 'pay_s_pd';

    const TRANSACTION_TYPE_WAIVE    = 'trt_waive';
    const TRANSACTION_TYPE_PAYMENT  = 'trt_payment';
    const TRANSACTION_TYPE_REFUND   = 'trt_refund';
    const TRANSACTION_TYPE_REVERSAL = 'trt_reversal';
    const TRANSACTION_TYPE_OTHER    = 'trt_other';


    /**
     * Goods or PSV keys
     */
    const LICENCE_CATEGORY_GOODS_VEHICLE = 'lcat_gv';
    const LICENCE_CATEGORY_PSV = 'lcat_psv';

    /**
     * Licence statuses
     */
    const LICENCE_STATUS_UNDER_CONSIDERATION = 'lsts_consideration';
    const LICENCE_STATUS_NOT_SUBMITTED = 'lsts_not_submitted';
    const LICENCE_STATUS_SUSPENDED = 'lsts_suspended';
    const LICENCE_STATUS_VALID = 'lsts_valid';
    const LICENCE_STATUS_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_GRANTED = 'lsts_granted';
    const LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION = 'lsts_surr_consideration';
    const LICENCE_STATUS_SURRENDERED = 'lsts_surrendered';
    const LICENCE_STATUS_WITHDRAWN = 'lsts_withdrawn';
    const LICENCE_STATUS_REFUSED = 'lsts_refused';
    const LICENCE_STATUS_REVOKED = 'lsts_revoked';
    const LICENCE_STATUS_NOT_TAKEN_UP = 'lsts_ntu';
    const LICENCE_STATUS_TERMINATED = 'lsts_terminated';
    const LICENCE_STATUS_CONTINUATION_NOT_SOUGHT = 'lsts_cns';
    const LICENCE_STATUS_UNLICENSED = 'lsts_unlicenced';
    const LICENCE_STATUS_CONSIDERATION = 'lsts_consideration';
    const LICENCE_STATUS_CANCELLED = 'lsts_cancelled';

    /**
     * Application statuses
     */
    const APPLICATION_STATUS_NOT_SUBMITTED = 'apsts_not_submitted';
    // this status will be displayed everywhere as Awaiting grant fee as per OLCS-12606
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';
    // this status will be displayed everywhere as Granted as per OLCS-12606
    const APPLICATION_STATUS_VALID = 'apsts_valid';
    const APPLICATION_STATUS_WITHDRAWN = 'apsts_withdrawn';
    const APPLICATION_STATUS_REFUSED = 'apsts_refused';
    const APPLICATION_STATUS_NOT_TAKEN_UP = 'apsts_ntu';
    const APPLICATION_STATUS_CANCELLED = 'apsts_cancelled';

    /**
     * Application withdraw reasons
     */
    const APPLICATION_WITHDRAW_REASON_WITHDRAWN = 'withdrawn';

    /**
     * Variation section statuses
     */
    const VARIATION_STATUS_UNCHANGED = 0;
    const VARIATION_STATUS_REQUIRES_ATTENTION = 1;
    const VARIATION_STATUS_UPDATED = 2;

    /**
     * Variation types
     */
    const VARIATION_TYPE_DIRECTOR_CHANGE = 'vtyp_director_change';

    /**
     * Grant authorities
     */
    const GRANT_AUTHORITY_DELEGATED = 'grant_authority_dl';
    const GRANT_AUTHORITY_TC = 'grant_authority_tc';
    const GRANT_AUTHORITY_TR = 'grant_authority_tr';

    /**
     * Transport Manager Application
     */
    const TMA_SIGN_AS_TM = 'tma_sign_as_tm';
    const TMA_SIGN_AS_OP = 'tma_sign_as_op';
    const TMA_SIGN_AS_TM_OP = 'tma_sign_as_top';

    const TMA_STATUS_INCOMPLETE = 'tmap_st_incomplete';
    const TMA_STATUS_AWAITING_SIGNATURE = 'tmap_st_awaiting_signature';
    const TMA_STATUS_TM_SIGNED = 'tmap_st_tm_signed';
    const TMA_STATUS_OPERATOR_SIGNED = 'tmap_st_operator_signed';
    const TMA_STATUS_POSTAL_APPLICATION = 'tmap_st_postal_application';
    const TMA_STATUS_RECEIVED = 'tmap_st_received';
    const TMA_STATUS_DETAILS_SUBMITTED = 'tmap_st_details_submitted';
    const TMA_STATUS_DETAILS_CHECKED = 'tmap_st_details_checked';
    const TMA_STATUS_OPERATOR_APPROVED = 'tmap_st_operator_approved';

    /**
     * Condition and Undertakings
     */
    const ATTACHED_TO_LICENCE = 'cat_lic';
    const ATTACHED_TO_OPERATING_CENTRE = 'cat_oc';

    const ADDED_VIA_CASE = 'cav_case';
    const ADDED_VIA_LICENCE = 'cav_lic';
    const ADDED_VIA_APPLICATION = 'cav_app';

    const TYPE_CONDITION = 'cdt_con';
    const TYPE_UNDERTAKING = 'cdt_und';

    /**
     * Organisation types
     */
    const ORG_TYPE_REGISTERED_COMPANY = 'org_t_rc';
    const ORG_TYPE_SOLE_TRADER = 'org_t_st';
    const ORG_TYPE_LLP = 'org_t_llp';
    const ORG_TYPE_PARTNERSHIP = 'org_t_p';
    const ORG_TYPE_OTHER = 'org_t_pa';
    const ORG_TYPE_IRFO = 'org_t_ir';
    const ORG_TYPE_RC = 'org_t_rc';

    /**
     * Schedule41
     */
    const S4_STATUS_APPROVED = 's4_sts_approved';
    const S4_STATUS_REFUSED = 's4_sts_refused';

    /**
     * Bus Reg Status
     */
    const BUSREG_STATUS_NEW = 'breg_s_new';
    const BUSREG_STATUS_VARIATION = 'breg_s_var';
    const BUSREG_STATUS_CANCELLATION = 'breg_s_cancellation';
    const BUSREG_STATUS_ADMIN = 'breg_s_admin';
    const BUSREG_STATUS_REGISTERED = 'breg_s_registered';
    const BUSREG_STATUS_REFUSED = 'breg_s_refused';
    const BUSREG_STATUS_WITHDRAWN = 'breg_s_withdrawn';
    const BUSREG_STATUS_CNS = 'breg_s_cns';
    const BUSREG_STATUS_CANCELLED = 'breg_s_cancelled';

    /**
     * EBSR Status
     */
    const EBSR_STATUS_PROCESSED = 'ebsrs_processed';
    const EBSR_STATUS_PROCESSING = 'ebsrs_processing';
    const EBSR_STATUS_VALIDATING = 'ebsrs_validating';
    const EBSR_STATUS_SUBMITTED = 'ebsrs_submitted';
    const EBSR_STATUS_FAILED = 'ebsrs_failed';

    /**
     * Role permissions
     */
    const PERMISSION_SELFSERVE_PARTNER_ADMIN = 'partner-admin';
    const PERMISSION_SELFSERVE_PARTNER_USER = 'partner-user';
    const PERMISSION_SELFSERVE_LVA = 'selfserve-lva';
    const PERMISSION_SELFSERVE_TM_DASHBOARD = 'selfserve-tm-dashboard';
    const PERMISSION_SELFSERVE_DASHBOARD = 'selfserve-nav-dashboard';
    const PERMISSION_SYSTEM_ADMIN = 'system-admin';
    const PERMISSION_INTERNAL_ADMIN = 'internal-admin';
    const PERMISSION_INTERNAL_IRHP_ADMIN = 'internal-irhp-admin';
    const PERMISSION_INTERNAL_EDIT = 'internal-edit';
    const PERMISSION_INTERNAL_VIEW = 'internal-view';
    const PERMISSION_INTERNAL_CASE = 'internal-case';
    const PERMISSION_INTERNAL_USER = 'internal-user';
    const PERMISSION_INTERNAL_DOCUMENTS = 'internal-documents';
    const PERMISSION_INTERNAL_NOTES = 'internal-notes';
    const PERMISSION_INTERNAL_PERMITS = 'internal-permits';
    const PERMISSION_INTERNAL_PUBLICATIONS = 'internal-publications';
    const PERMISSION_INTERNAL_LIMITED_READ_ONLY = 'internal-limited-read-only';
    const PERMISSION_INTERNAL_FEES = 'internal-fees';
    const PERMISSION_CAN_MANAGE_USER_INTERNAL = 'can-manage-user-internal';
    const PERMISSION_SELFSERVE_EBSR_UPLOAD = 'selfserve-ebsr-upload';
    const PERMISSION_SELFSERVE_EBSR_DOCUMENTS = 'selfserve-ebsr-documents';

    /**
     * User Roles
     */
    const ROLE_INTERNAL_LIMITED_READ_ONLY = 'internal-limited-read-only';
    const ROLE_INTERNAL_READ_ONLY = 'internal-read-only';
    const ROLE_INTERNAL_CASE_WORKER = 'internal-case-worker';
    const ROLE_INTERNAL_ADMIN = 'internal-admin';
    const ROLE_OPERATOR_ADMIN = 'operator-admin';
    const ROLE_OPERATOR_USER = 'operator-user';
    const ROLE_OPERATOR_TM = 'operator-tm';
    const ROLE_PARTNER_ADMIN = 'partner-admin';
    const ROLE_PARTNER_USER = 'partner-user';
    const ROLE_LOCAL_AUTHORITY_ADMIN = 'local-authority-admin';
    const ROLE_LOCAL_AUTHORITY_USER = 'local-authority-user';
    const ROLE_ANON = 'anon';

    /**
     * User types
     */
    const USER_TYPE_INTERNAL = 'internal';
    const USER_TYPE_LOCAL_AUTHORITY = 'local-authority';
    const USER_TYPE_PARTNER = 'partner';

    /**
     * Operator CPID.
     */
    const OPERATOR_CPID_CENTRAL = 'op_cpid_central_government';
    const OPERATOR_CPID_LOCAL = 'op_cpid_local_government';
    const OPERATOR_CPID_CORPORATION = 'op_cpid_public_corporation';
    const OPERATOR_CPID_DEFAULT = 'op_cpid_default';
    const OPERATOR_CPID_ALL = 'op_cpid_all';

    /**
     * Contact Details
     */
    const TRANSPORT_MANAGER_TYPE_EXTERNAL = 'tm_t_e';
    const TRANSPORT_MANAGER_TYPE_BOTH = 'tm_t_b';

    const TRANSPORT_MANAGER_STATUS_CURRENT = 'tm_s_cur';
    const TRANSPORT_MANAGER_STATUS_DISQUALIFIED = 'tm_s_dis';
    const TRANSPORT_MANAGER_STATUS_REMOVED = 'tm_s_rem';

    const CONTACT_TYPE_PARTNER = 'ct_partner';
    const CONTACT_TYPE_REGISTERED = 'ct_reg';

    /**
     * IRFO Stock Control
     */
    const IRFO_STOCK_CONTROL_STATUS_IN_STOCK = 'irfo_perm_s_s_in_stock';
    const IRFO_STOCK_CONTROL_STATUS_ISSUED = 'irfo_perm_s_s_issued';
    const IRFO_STOCK_CONTROL_STATUS_VOID = 'irfo_perm_s_s_void';
    const IRFO_STOCK_CONTROL_STATUS_RETURNED = 'irfo_perm_s_s_ret';

    // PSV Vehicle sizes
    const PSV_VEHICLE_SIZE_SMALL = 'psvvs_small';
    const PSV_VEHICLE_SIZE_MEDIUM_LARGE = 'psvvs_medium_large';
    const PSV_VEHICLE_SIZE_BOTH = 'psvvs_both';

    /**
     * IRFO Status
     */
    const IRFO_PSV_AUTH_STATUS_APPROVED = 'irfo_auth_s_approved';
    const IRFO_PSV_AUTH_STATUS_CNS = 'irfo_auth_s_cns';
    const IRFO_PSV_AUTH_STATUS_GRANTED = 'irfo_auth_s_granted';
    const IRFO_PSV_AUTH_STATUS_PENDING = 'irfo_auth_s_pending';
    const IRFO_PSV_AUTH_STATUS_REFUSED = 'irfo_auth_s_refused';
    const IRFO_PSV_AUTH_STATUS_RENEW = 'irfo_auth_s_renew';
    const IRFO_PSV_AUTH_STATUS_WITHDRAWN = 'irfo_auth_s_withdrawn';

    /**
     * Applied VIA
     */
    const APPLIED_VIA_POST = 'applied_via_post';
    const APPLIED_VIA_PHONE = 'applied_via_phone';
    const APPLIED_VIA_SELFSERVE = 'applied_via_selfserve';

    /**
     * Impounding types
     */
    const IMPOUNDING_TYPE_HEARING = 'impt_hearing';
    const IMPOUNDING_TYPE_PAPER = 'impt_paper';

    /**
     * Convictions
     */
    const CONVICTION_CATEGORY_USER_DEFINED = 'conv_c_cat_1144';

    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    const COMMUNITY_LICENCE_STATUS_PENDING = 'cl_sts_pending';
    const COMMUNITY_LICENCE_STATUS_ACTIVE = 'cl_sts_active';
    const COMMUNITY_LICENCE_STATUS_EXPIRED = 'cl_sts_expired';
    const COMMUNITY_LICENCE_STATUS_WITHDRAWN = 'cl_sts_withdrawn';
    const COMMUNITY_LICENCE_STATUS_SUSPENDED = 'cl_sts_suspended';
    const COMMUNITY_LICENCE_STATUS_VOID = 'cl_sts_annulled';
    const COMMUNITY_LICENCE_STATUS_RETURNDED = 'cl_sts_returned';

    /**
     * Erru
     */
    const ERRU_RESPONSE_SENT = 'erru_case_t_msirs';
    const ERRU_RESPONSE_SENDING_FAILED = 'erru_case_t_msirsf';
    const ERRU_RESPONSE_QUEUED = 'erru_case_t_msirnys';

    const ERROR_FEE_NOT_CREATED = 'AP-FEE-NOT-CREATED';

    const UNDERTAKINGS_KEY = 'undertakings';

    const SIGNATURE_TYPE_PHYSICAL_SIGNATURE = 'sig_physical_signature';
    const SIGNATURE_TYPE_DIGITAL_SIGNATURE = 'sig_digital_signature';
    const SIGNATURE_TYPE_NOT_REQUIRED = 'sig_signature_not_required';

    const ERR_NO_FEES = 'ERR_NO_FEES';
    const ERR_WAIT = 'ERR_WAIT';

    const AD_POST = 0;
    const AD_UPLOAD_NOW = 1;
    const AD_UPLOAD_LATER = 2;

    const PHONE_TYPE_PRIMARY = 'phone_t_primary';
    const PHONE_TYPE_SECONDARY = 'phone_t_secondary';

    const CONTINUATIONS_DISPLAY_PERSON_COUNT = 10;
    const CONTINUATIONS_DISPLAY_VEHICLES_COUNT = 10;
    const CONTINUATIONS_DISPLAY_USERS_COUNT = 10;
    const CONTINUATIONS_DISPLAY_OPERATING_CENTRES_COUNT = 10;
    const CONTINUATIONS_DISPLAY_SAFETY_INSPECTORS_COUNT = 10;
    const CONTINUATIONS_DISPLAY_TM_COUNT = 10;

    const LICENCE_CHECKLIST_TYPE_OF_LICENCE = 'type_of_licence';
    const LICENCE_CHECKLIST_BUSINESS_TYPE = 'business_type';
    const LICENCE_CHECKLIST_BUSINESS_DETAILS = 'business_details';
    const LICENCE_CHECKLIST_ADDRESSES = 'addresses';
    const LICENCE_CHECKLIST_PEOPLE = 'people';
    const LICENCE_CHECKLIST_VEHICLES = 'vehicles';
    const LICENCE_CHECKLIST_USERS = 'users';
    const LICENCE_CHECKLIST_OPERATING_CENTRES = 'operating_centres';
    const LICENCE_CHECKLIST_OPERATING_CENTRES_AUTHORITY = 'operating_centres_authority';
    const LICENCE_CHECKLIST_TRANSPORT_MANAGERS = 'transport_managers';
    const LICENCE_CHECKLIST_SAFETY_INSPECTORS = 'safety';
    const LICENCE_CHECKLIST_SAFETY_DETAILS = 'safety_details';
    const LICENCE_SAFETY_INSPECTOR_EXTERNAL = 'tach_external';

    const RESULT_LICENCE_CONTINUED = 'licence_continued';

    const CONTINUATION_STATUS_GENERATED = 'con_det_sts_printed';
    const CONTINUATION_STATUS_COMPLETE = 'con_det_sts_complete';

    const PTR_ACTION_TO_BE_TAKEN_REVOKE = 'ptr_action_to_be_taken_revoke';
    const PTR_ACTION_TO_BE_TAKEN_PI = 'ptr_action_to_be_taken_pi';
    const PTR_ACTION_TO_BE_TAKEN_WARNING = 'ptr_action_to_be_taken_warning';
    const PTR_ACTION_TO_BE_TAKEN_NFA = 'ptr_action_to_be_taken_nfa';
    const PTR_ACTION_TO_BE_TAKEN_OTHER = 'ptr_action_to_be_taken_other';

    /**
     * Permit statuses
     */
    const PERMIT_VALID = 'permit_valid';
    const PERMIT_EXPIRED = 'permit_expired';
    const PERMIT_AWAITING = 'permit_awaiting';
    const PERMIT_NYS = 'permit_nys';

    /**
     * ECMT Permit application statuses
     */
    const PERMIT_APP_STATUS_CANCELLED = 'permit_app_cancelled';
    const PERMIT_APP_STATUS_NOT_YET_SUBMITTED = 'permit_app_nys';
    const PERMIT_APP_STATUS_UNDER_CONSIDERATION = 'permit_app_uc';
    const PERMIT_APP_STATUS_WITHDRAWN = 'permit_app_withdrawn';
    const PERMIT_APP_STATUS_AWAITING_FEE = 'permit_app_awaiting';
    const PERMIT_APP_STATUS_FEE_PAID = 'permit_app_fee_paid';
    const PERMIT_APP_STATUS_UNSUCCESSFUL = 'permit_app_unsuccessful';
    const PERMIT_APP_STATUS_ISSUING = 'permit_app_issuing';
    const PERMIT_APP_STATUS_VALID = 'permit_app_valid';
    const PERMIT_APP_STATUS_EXPIRED = 'permit_app_expired';

    const PERMIT_APP_WITHDRAW_REASON_UNPAID = 'permits_app_withdraw_not_paid';
    const PERMIT_APP_WITHDRAW_REASON_DECLINED = 'permits_app_withdraw_declined';
    const PERMIT_APP_WITHDRAW_REASON_USER = 'permits_app_withdraw_by_user';

    /**
     * ECMT Permit application international journey percentages
     */
    const ECMT_APP_JOURNEY_LESS_60 = 'inter_journey_less_60';
    const ECMT_APP_JOURNEY_60_90 = 'inter_journey_60_90';
    const ECMT_APP_JOURNEY_OVER_90 = 'inter_journey_more_90';

    const IRHP_PERMIT_STATUS_PENDING            = 'irhp_permit_pending';
    const IRHP_PERMIT_STATUS_AWAITING_PRINTING  = 'irhp_permit_awaiting_printing';
    const IRHP_PERMIT_STATUS_PRINTING           = 'irhp_permit_printing';
    const IRHP_PERMIT_STATUS_PRINTED            = 'irhp_permit_printed';
    const IRHP_PERMIT_STATUS_ERROR              = 'irhp_permit_error';
    const IRHP_PERMIT_STATUS_CEASED             = 'irhp_permit_ceased';
    const IRHP_PERMIT_STATUS_TERMINATED         = 'irhp_permit_terminated';
    const IRHP_PERMIT_STATUS_EXPIRED            = 'irhp_permit_expired';

    /**
     * ECMT Permit application sources
     */
    const ECMT_APP_SOURCE_SELFSERVE = 'app_source_selfserve';
    const ECMT_APP_SOURCE_INTERNAL = 'app_source_internal';

    //feature toggle statuses
    const FT_ACTIVE = 'always-active';
    const FT_INACTIVE = 'inactive';
    const FT_CONDITIONAL = 'conditionally-active';

    //Surrenders
    const SURRENDER_STATUS_START='surr_sts_start';
    const SURRENDER_STATUS_CONTACTS_COMPLETE='surr_sts_contacts_complete';
    const SURRENDER_STATUS_DISCS_COMPLETE='surr_sts_discs_complete';
    const SURRENDER_STATUS_LIC_DOCS_COMPLETE='surr_sts_lic_docs_complete';
    const SURRENDER_STATUS_COMM_LIC_DOCS_COMPLETE='surr_sts_comm_lic_docs_complete';
    const SURRENDER_STATUS_DETAILS_CONFIRMED='surr_sts_details_confirmed';
    const SURRENDER_STATUS_SUBMITTED='surr_sts_submitted';
    const SURRENDER_STATUS_SIGNED='surr_sts_signed';
    const SURRENDER_STATUS_WITHDRAWN='surr_sts_withdrawn';
    const SURRENDER_STATUS_APPROVED='surr_sts_approved';
    const SURRENDER_DOC_STATUS_DESTROYED='doc_sts_destroyed';
    const SURRENDER_DOC_STATUS_LOST='doc_sts_lost';
    const SURRENDER_DOC_STATUS_STOLEN='doc_sts_stolen';

    //IRHP Permit Type
    const ECMT_PERMIT_TYPE_ID = 1;
    const ECMT_SHORT_TERM_PERMIT_TYPE_ID = 2;
    const ECMT_REMOVAL_PERMIT_TYPE_ID = 3;
    const IRHP_BILATERAL_PERMIT_TYPE_ID = 4;
    const IRHP_MULTILATERAL_PERMIT_TYPE_ID = 5;
    const CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID = 6;
    const CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID = 7;

    // IRHP Permit Fee Types
    const IRHP_GV_APPLICATION_FEE_TYPE = 'IRHPGVAPP';
    const IRHP_GV_ISSUE_FEE_TYPE = 'IRHPGVISSUE';
    const IRFO_GV_FEE_TYPE = 'IRFOGVPERMIT';

    const EMISSIONS_CATEGORY_EURO5 = 'emissions_cat_euro5';
    const EMISSIONS_CATEGORY_EURO6 = 'emissions_cat_euro6';
    const EMISSIONS_CATEGORY_NA = 'emissions_cat_na';

    // Question data types
    const QUESTION_TYPE_STRING = 'question_type_string';
    const QUESTION_TYPE_INTEGER = 'question_type_integer';
    const QUESTION_TYPE_BOOLEAN = 'question_type_boolean';
    const QUESTION_TYPE_DATE = 'question_type_date';
    const QUESTION_TYPE_CUSTOM = 'question_type_custom';

    // user operating system
    const USER_OS_TYPE_WINDOWS_7 = 'windows_7';
    const USER_OS_TYPE_WINDOWS_10 = 'windows_10';

    // Business process
    const BUSINESS_PROCESS_APG = 'app_business_process_apg';
    const BUSINESS_PROCESS_APGG = 'app_business_process_apgg';
    const BUSINESS_PROCESS_APSG = 'app_business_process_apsg';

    const COMPLAIN_STATUS_OPEN = 'ecst_open';
    const COMPLAIN_STATUS_CLOSED = 'ecst_closed';

    const LICENCE_STATUS_RULE_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_RULE_REVOKED = 'lsts_revoked';
    const LICENCE_STATUS_RULE_SUSPENDED = 'lsts_suspended';

    /**
     * PSV types
     */
    const PSV_TYPE_SMALL  = 'vhl_t_a';
    const PSV_TYPE_MEDIUM = 'vhl_t_b';
    const PSV_TYPE_LARGE  = 'vhl_t_c';

    const TASK_ALLOCATION_TYPE_SIMPLE  = 'task_at_simple';
    const TASK_ALLOCATION_TYPE_MEDIUM  = 'task_at_medium';
    const TASK_ALLOCATION_TYPE_COMPLEX = 'task_at_complex';

    const INSPECTION_REPORT_TYPE_MAINTENANCE_REQUEST = 'insp_rep_t_maint';
    const INSPECTION_RESULT_TYPE_NEW = 'insp_res_t_new';

    const APPLICATION_TYPE_NEW = 0;
    const APPLICATION_TYPE_VARIATION = 1;

    const INTERIM_STATUS_REQUESTED = 'int_sts_requested';
    const INTERIM_STATUS_INFORCE = 'int_sts_in_force';
    const INTERIM_STATUS_REFUSED = 'int_sts_refused';
    const INTERIM_STATUS_REVOKED = 'int_sts_revoked';
    const INTERIM_STATUS_GRANTED = 'int_sts_granted';

    const WITHDRAWN_REASON_WITHDRAWN    = 'withdrawn';
    const WITHDRAWN_REASON_REG_IN_ERROR = 'reg_in_error';

    // Queue message statuses
    const QUEUE_STATUS_QUEUED = 'que_sts_queued';
    const QUEUE_STATUS_PROCESSING = 'que_sts_processing';
    const QUEUE_STATUS_COMPLETE = 'que_sts_complete';
    const QUEUE_STATUS_FAILED = 'que_sts_failed';

    const CONTINUATION_DETAIL_STATUS_PREPARED = 'con_det_sts_prepared';
    const CONTINUATION_DETAIL_STATUS_PRINTING = 'con_det_sts_printing';
    const CONTINUATION_DETAIL_STATUS_PRINTED = 'con_det_sts_printed';
    const CONTINUATION_DETAIL_STATUS_UNACCEPTABLE = 'con_det_sts_unacceptable';
    const CONTINUATION_DETAIL_STATUS_ACCEPTABLE = 'con_det_sts_acceptable';
    const CONTINUATION_DETAIL_STATUS_COMPLETE = 'con_det_sts_complete';
    const CONTINUATION_DETAIL_STATUS_ERROR = 'con_det_sts_error';

    const APPLICATION_COMPLETION_STATUS_NOT_STARTED = 0;
    const APPLICATION_COMPLETION_STATUS_INCOMPLETE = 1;
    const APPLICATION_COMPLETION_STATUS_COMPLETE = 2;

    const BILATERAL_PERMIT_USAGE = 'bi-permit-usage';
    const BILATERAL_NUMBER_OF_PERMITS = 'bi-number-of-permits';

    // journey
    const JOURNEY_SINGLE = 'journey_single';
    const JOURNEY_MULTIPLE = 'journey_multiple';
}
