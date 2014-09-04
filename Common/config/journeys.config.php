<?php

// @TODO now we are sharing this between applications we may need to refactor in here

$journeysDirectory = __DIR__ . '/journeys/*.journey.php';

$allRoutes = [];

$journeyArray = array_map(
    function ($file) {
        return include $file;
    },
    glob($journeysDirectory)
);

$filter = new \Zend\Filter\Word\CamelCaseToDash();

$controllers = array();

$journeys = array();

foreach ($journeyArray as $journey) {

    foreach ($journey as $name => $details) {

        $journeys[$name] = $details;

        $journeyNamespace = $details['namespace'];

        $controller = $journeyNamespace . '\\' . $name . '\\' . $name . 'Controller';

        $controllers[$controller] = $controller;

        $allRoutes[$name] = array(
            'type' => 'segment',
            'options' => array(
                'route' => '/' . strtolower($filter->filter($name)) . '[/:' . $details['identifier'] . '][/]',
                'constraints' => array(
                    $details['identifier'] => '[0-9]+'
                ),
                'defaults' => array(
                    'controller' => $controller,
                    'action' => 'index'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array()
        );

        foreach ($details['sections'] as $sectionName => $sectionDetails) {

            $namespace = $journeyNamespace . '\\' . $sectionName;

            $controller = $namespace . '\\' . $sectionName . 'Controller';
            $controllers[$controller] = $controller;

            $allRoutes[$name]['child_routes'][$sectionName] = array(
                'type' => 'segment',
                'options' => array(
                    'route' => strtolower($filter->filter($sectionName)) . '[/]',
                    'defaults' => array(
                        'controller' => $controller,
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            );

            foreach ($sectionDetails['subSections'] as $subSectionName => $subSectionDetails) {

                $controller = $namespace . '\\' . $subSectionName . 'Controller';
                $controllers[$controller] = $controller;

                $allRoutes[$name]['child_routes'][$sectionName]['child_routes'][$subSectionName] = array(
                    'type' => 'segment',
                    'options' => array(
                        'route' => strtolower($filter->filter($subSectionName)) . '[/:action][/:id][/]',
                        'constraints' => array(
                            'id' => '[0-9]+'
                        ),
                        'defaults' => array(
                            'controller' => $controller,
                            'action' => 'index'
                        )
                    )
                );
            }
        }
    }
}

return array($allRoutes, $controllers, $journeys);
