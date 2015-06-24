<?php

namespace Common;

/**
 * Holds the Ref Data constants required by the web app
 */
class RefData
{
    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';

    const ORG_TYPE_LLP = 'org_t_llp';

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
    const FEE_STATUS_WAIVE_RECOMMENDED = 'lfs_wr';
    const FEE_STATUS_WAIVED            = 'lfs_w';
    const FEE_STATUS_CANCELLED         = 'lfs_cn';

    const PAYMENT_STATUS_OUTSTANDING = 'pay_s_os';
    const PAYMENT_STATUS_CANCELLED   = 'pay_s_cn';
    const PAYMENT_STATUS_LEGACY      = 'pay_s_leg';
    const PAYMENT_STATUS_FAILED      = 'pay_s_fail';
    const PAYMENT_STATUS_PAID        = 'pay_s_pd';
}
