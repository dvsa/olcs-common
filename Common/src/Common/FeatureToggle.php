<?php

namespace Common;

/**
 * Holds the feature toggle config keys as a constant
 */
class FeatureToggle
{
    const ADMIN_PERMITS = 'admin_permits';
    const INTERNAL_ECMT = 'internal_ecmt';
    const INTERNAL_PERMITS = 'internal_permits';
    const SELFSERVE_ECMT = 'ss_ecmt';
    const SELFSERVE_PERMITS = 'ss_permits';
    const BACKEND_ECMT = 'back_ecmt';
    const BACKEND_PERMITS = 'back_permits';

    const INTERNAL_SURRENDER = 'internal_surrender';
    const SELFSERVE_SURRENDER = 'ss_surrender';
    const BACKEND_SURRENDER = 'back_surrender';
}
