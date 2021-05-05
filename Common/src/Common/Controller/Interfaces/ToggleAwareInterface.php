<?php

namespace Common\Controller\Interfaces;

interface ToggleAwareInterface
{
    /**
     * Gets the configuration for feature toggles which should be used to enable/disable actions within a controller.
     *
     * Format: ['ACTION' => [TOGGLES_THAT_NEED_TO_BE_ENABLED]]
     *
     *
     * Hide all controller actions behind one or more feature toggles.
     *
     * To put all controller actions behind one or more feature toggles, you can use the "default" action key. This
     * will mean that all actions will be inaccessible unless all feature toggles defined are enabled.
     *
     * Example: ['default' => ['my-feature-default-toggle']]
     *
     *
     * Hide a single controller action behind one or more feature toggles.
     *
     * The following example would only hide the "index" action when the "my-feature-index-toggle" feature toggle is
     * disabled.
     *
     * Example: ['index' => ['my-feature-index-toggle']]
     *
     * @return array
     */
    public function getToggleConfig(): array; // @todo return a FeatureToggleConfiguration?
}
