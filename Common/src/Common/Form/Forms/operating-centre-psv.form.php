<?php

$operatingCentreForm = include(dirname(__FILE__) . '/operating-centre.form.php');

//reconfigure form for psv licence type
$operatingCentreForm['operating-centre-psv'] = $operatingCentreForm['operating-centre'];
unset($operatingCentreForm['operating-centre']);
unset($operatingCentreForm['operating-centre-psv']['fieldsets']['authorised-vehicles']['elements']['no-of-trailers']);
$operatingCentreForm['operating-centre-psv']['fieldsets']['authorised-vehicles']['elements']['no-of-vehicles']['type'] = 'vehiclesNumberPsv';

//\Zend\Debug\Debug::dump($operatingCentreForm);exit;
return $operatingCentreForm;