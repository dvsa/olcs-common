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

    const TRANSACTION_STATUS_COMPLETE    = 'pay_s_pd';
    const TRANSACTION_STATUS_OUTSTANDING = 'pay_s_os';
    const TRANSACTION_STATUS_CANCELLED   = 'pay_s_cn';
    const TRANSACTION_STATUS_FAILED      = 'pay_s_fail';
    const TRANSACTION_STATUS_PAID        = 'pay_s_pd';

    const TRANSACTION_TYPE_WAIVE   = 'trt_waive';
    const TRANSACTION_TYPE_PAYMENT = 'trt_payment';

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
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';
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
     * Role permissions
     */
    const PERMISSION_SELFSERVE_PARTNER_ADMIN = 'partner-admin';
    const PERMISSION_SELFSERVE_PARTNER_USER = 'partner-user';

    /**
     * Phone contact types
     */
    const TYPE_BUSINESS = 'phone_t_tel';
    const TYPE_HOME = 'phone_t_home';
    const TYPE_MOBILE = 'phone_t_mobile';
    const TYPE_FAX = 'phone_t_fax';

    /**
     * Operator CPID.
     */
    const OPERATOR_CPID_CENTRAL = 'op_cpid_central';
    const OPERATOR_CPID_LOCAL = 'op_cpid_local';
    const OPERATOR_CPID_CORPORATION = 'op_cpid_corporation';
    const OPERATOR_CPID_DEFAULT = 'op_cpid_default';
    const OPERATOR_CPID_ALL = 'op_cpid_all';

    const TRANSPORT_MANAGER_TYPE_EXTERNAL = 'tm_t_e';
    const TRANSPORT_MANAGER_TYPE_BOTH = 'tm_t_b';

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

}
