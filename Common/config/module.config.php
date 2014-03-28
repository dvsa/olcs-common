<?php
return array(
    'view_helpers' => array(
        'invokables' => array(
            'form' => 'Common\Form\View\Helper\Form',
            'formRow' => 'Common\Form\View\Helper\FormRow',
            'formElement' => 'Common\Form\View\Helper\FormElement',
            'formElementErrors' => 'Common\Form\View\Helper\FormElementErrors',
            'formErrors' => 'Common\Form\View\Helper\FormErrors',
            'htmlTable'     => 'Common\View\Helper\HtmlTable',
            'crudHtmlTable' => 'Common\View\Helper\CrudHtmlTable',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
           'partials/view' => __DIR__ . '/../view',
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'forms_path' => '/../../Form/Forms/',
    'fieldsets_path' => '/../../Form/Fieldsets/',
    'static-list-data' => include __DIR__ . '/list-data/static-list-data.php',
    'form' => array(
        'elements' =>  include __DIR__ . '/../src/Form/Elements/getElements.php',
    )
);
