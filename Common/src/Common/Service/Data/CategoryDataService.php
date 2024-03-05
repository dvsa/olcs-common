<?php

namespace Common\Service\Data;

/**
 * Category Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CategoryDataService
{
    public const CATEGORY_SERVICE = 'Category';
    public const SUB_CATEGORY_SERVICE = 'SubCategory';

    public const CATEGORY_LICENSING = 1;
    public const CATEGORY_COMPLIANCE = 2;
    public const CATEGORY_BUS_REGISTRATION = 3;
    public const CATEGORY_PERMITS = 4;
    public const CATEGORY_TRANSPORT_MANAGER = 5;
    public const CATEGORY_ENVIRONMENTAL = 7;
    public const CATEGORY_IRFO = 8;
    public const CATEGORY_APPLICATION = 9;
    public const CATEGORY_SUBMISSION = 10;
    public const CATEGORY_REPORT = 12;

    // @NOTE create constants for all sub categories as required. Only a subset
    // will ever be needed programatically so this list should be manageable
    public const TASK_SUB_CATEGORY_APPLICATION_ADDRESS_CHANGE_DIGITAL = 3;
    public const TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE = 11;
    public const TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL = 14;
    public const TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL = 15;
    public const TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL = 25;
    public const TASK_SUB_CATEGORY_HEARINGS_APPEALS = 49;
    public const TASK_SUB_CATEGORY_DECISION = 96;
    public const TASK_SUB_CATEGORY_RECOMMENDATION = 97;
    public const TASK_SUB_CATEGORY_ASSIGNMENT = 114;
    public const TASK_SUB_CATEGORY_REVIEW_COMPLAINT = 61;
    public const TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK = 77;
    public const TASK_SUB_CATEGORY_INSPECTION_REQUEST_SEMINAR = 78;

    public const SCAN_SUB_CATEGORY_CHANGE_OF_ENTITY = 85;

    public const DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL = 5;
    public const DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST = 91;
    public const DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL = 112;
    public const DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION = 98;
    public const DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL = 100;
    public const DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL = 13;
    public const DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS = 74;
    public const DOC_SUB_CATEGORY_OTHER_DOCUMENTS = 79;
    public const DOC_SUB_CATEGORY_FEE_REQUEST = 110;
    public const DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE = 74;

    public const DOC_SUB_CATEGORY_LETTER = 203;
}
