<?php

namespace Common\Controller;

use Zend\Code\Generator\PropertyGenerator;
use Zend\Filter\Word\DashToCamelCase;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\InputFilter\InputProviderInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Null;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Stdlib\ArrayUtils;

/**
 * Class FormRewriteController
 * @package Common\Controller
 * @TO-DO when the forms have all been converted, this controller, its entry in modules.config and the console route to
 * it can all be removed
 */
class FormRewriteController extends AbstractActionController
{
    protected $log;
    protected $basePath;
    protected $namespace;

    public function init()
    {
        $basePaths = [
            'olcs' => 'olcs-internal/module/Olcs/src/',
            'common' => 'olcs-common/Common/src/Common/',
            'selfserve' => 'olcs-selfserve/module/SelfServe/src/SelfServe/'
        ];

        $namespaces = [
            'olcs' => 'Olcs',
            'common' => 'Common',
            'selfserve' => 'SelfServe'
        ];

        $formNamespace =  $this->params()->fromRoute('formnamespace');

        $this->basePath = substr(__DIR__, 0, strpos(__DIR__, 'olcs-common')) . $basePaths[$formNamespace];
        $this->namespace = $namespaces[$formNamespace];

        $directories = ['/Form/Model/', '/Form/Model/Form/', '/Form/Model/Fieldset/'];

        foreach ($directories as $dir) {
            $fullDir = $this->basePath . $dir;
            if (!file_exists($fullDir)) {
                mkdir($fullDir);
                $this->getLog()->info('Created output Directory: '. $fullDir);
            }
        }
    }

    public function indexAction()
    {
        $this->init();

        $glob = $this->basePath . '/Form/Forms/';

        foreach (glob($glob. '*.form.php') as $formFile) {
            $formName =  substr(
                $formFile,
                strpos($formFile, '/Forms/')+7, strpos($formFile, '.form')- (strpos($formFile, '/Forms/')+7)
            );

            $this->getLog()->info('Processing: ' . $this->normaliseName($formName) . '(' . $formFile . ')');

            $formData = include $formFile;
            $this->buildFormClass(current($formData));
        }

        echo "\n\n";
    }

    protected function buildFormClass($data)
    {
        $formName = $this->normaliseName($data['name'], true);
        $fieldsets = [];

        if (isset($data['fieldsets'])) {
            foreach ($data['fieldsets'] as $fieldset) {
                $fieldsetClass = $this->buildFieldsetClass($fieldset, $formName);
                $fieldsets[] = [
                    'classname' => $fieldsetClass['classname'],
                    'name' => isset($fieldset['name'])?$fieldset['name']:$fieldsetClass['name']
                ];
            }

        }

        $classGenerator = new ClassGenerator();
        $classGenerator->setName($formName);

        $tags = [];

        if (isset($data['name'])) {
            $tags['tags'][] = ['name'=> sprintf('Form\\Name("%s")', $data['name'])];
        }

        if (isset($data['attributes'])) {
            $tags['tags'][] =
                ['name' => sprintf("Form\\Attributes(%s)", $this->encodeOptionBlock($data['attributes']))];
        }

        if (isset($data['type'])) {
            $tags['tags'][] = ['name' => sprintf('Form\\Type("%s")', $data['type'])];
        }

        $classGenerator->setDocBlock(DocBlockGenerator::fromArray($tags));

        if (isset($data['elements'])) {
            foreach ($data['elements'] as $name => $element) {
                $element['name'] = isset($element['name'])? $element['name']: $name;
                $classGenerator->addPropertyFromGenerator($this->buildElement($element));
            }
        }

        foreach ($fieldsets as $fieldset) {
            $tags = [
                'tags'=> [
                    ['name'=>sprintf('Form\Name("%s")', $fieldset['name'])],
                    ['name'=>sprintf(
                        'Form\ComposedObject("%s")',
                        $this->namespace . '\Form\Fieldset\\' . $fieldset['classname']
                    )
                    ]
                ]
            ];

            $propertyGenerator = new PropertyGenerator($this->normaliseName($fieldset['name']));
            $propertyGenerator->setDocBlock(DocBlockGenerator::fromArray($tags));
            $classGenerator->addPropertyFromGenerator($propertyGenerator);
        }

        unset($data['elements'], $data['attributes'], $data['name'], $data['fieldsets'], $data['type']);
        if (!empty($data)) {
            $this->getLog()->err('Unhandled properties for form: '. json_encode(array_keys($data)));
        }

        $fileGenerator = new FileGenerator();
        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setNamespace($this->namespace . '\Form\Model\Form');
        $fileGenerator->setUse('Zend\Form\Annotation', 'Form');

        $fileGenerator->setFilename($this->basePath . 'Form/Model/Form/' . $classGenerator->getName() . '.php');

        $this->write($fileGenerator);

        unset($fileGenerator, $classGenerator);
    }

    protected function buildElement($data)
    {
        $propertyGenerator = new PropertyGenerator($this->normaliseName($data['name']));
        $tags = [];

        $data = $this->updateElementWithTemplateSpec($data);
        $data = $this->mutateElementType($data);

        $mergeOptions = array('label', 'label_attributes', 'description', 'hint', 'route', 'value-label', 'help-block');

        foreach ($mergeOptions as $option) {
            if (isset($data[$option])) {
                $data['options'][$option] = $data[$option];
                unset($data[$option]);
            }
        }

        $mergeAttributes = array('class', 'placeholder', 'data-container-class', 'value');

        foreach ($mergeAttributes as $attribute) {
            if (isset($data[$attribute])) {
                $data['attributes'][$attribute] = $data[$attribute];
                unset($data[$attribute]);
            }
        }

        if (isset($data['value_options'])) {
            if (is_string($data['value_options'])) {
                $data['type'] = 'DynamicSelect';
                $data['options']['category'] = $data['value_options'];
                unset($data['options']['value_options']);
            } else {
                $data['options']['value_options'] = $data['value_options'];
            }
            unset($data['value_options']);
        }

        if (isset($data['attributes'])) {
            $tags['tags'][] =
                ['name' => sprintf("Form\\Attributes(%s)", $this->encodeOptionBlock($data['attributes']))];
        }

        if (isset($data['options']['required'])) {
            $data['required'] = $data['options']['required'];
            unset($data['options']['required']);
        }

        if (isset($data['options']) && !empty($data['options'])) {
            $tags['tags'][] = ['name' => sprintf("Form\\Options(%s)", $this->encodeOptionBlock($data['options']))];
        }

        if (isset($data['required']) && !$data['required']) {
            $tags['tags'][] = ['name' => 'Form\Required(false)'];
        }

        if (isset($data['allow_empty']) && $data['allow_empty']) {
            $tags['tags'][] = ['name' => 'Form\AllowEmpty(true)'];
        }

        if (isset($data['continue_if_empty']) && $data['continue_if_empty']) {
            $tags['tags'][] = ['name' => 'Form\ContinueIfEmpty(true)'];
            $this->getLog()->warn('Element is using ContinueIfEmpty annotation, this isn\'t supported until ZF2.4');
        }

        if (isset($data['type'])) {
            $tags['tags'][] = ['name' => sprintf('Form\Type("%s")', $data['type'])];
        }

        if (isset($data['input_filters'])) {
            foreach ($data['input_filters'] as $filterSpec) {
                $tags['tags'][] = ['name' => sprintf('Form\Filter(%s)', $this->encodeOptionBlock($filterSpec))];
            }
        }

        if (isset($data['validators'])) {
            foreach ($data['validators'] as $validateSpec) {
                if (!is_array($validateSpec)) {
                    $this->getLog()->err('Validation spec is not an array for element: ' . $data['name']);
                } else {
                    $tags['tags'][] =
                        ['name' => sprintf('Form\Validator(%s)', $this->encodeOptionBlock($validateSpec))];
                }
            }
        }

        $propertyGenerator->setDocBlock(DocBlockGenerator::fromArray($tags));

        if (isset($data['enable'])) {
            $this->getLog()->warn('Ignoring property `enable` for element: ' . $data['name']);
        }

        unset(
            $data['type'],
            $data['name'],
            $data['label'],
            $data['class'],
            $data['options'],
            $data['attributes'],
            $data['required'],
            $data['filters'],
            $data['enable'],
            $data['input_filters'],
            $data['validators'],
            $data['allow_empty'],
            $data['continue_if_empty']
        );
        if (!empty($data)) {
            $this->getLog()->err(
                'Unhandled properties for element: '. $propertyGenerator->getName() . ' ' .
                json_encode(array_keys($data))
            );
        }

        return $propertyGenerator;
    }

    protected function buildFieldsetClass($data, $formName)
    {
        if (isset($data['type'])) {
            $config = $this->getServiceLocator()->get('Config');
            $path = $config['fieldsets_path'] . $data['type'] . '.fieldset.php';
            $fieldsetConfig = include $path;
            unset($data['type']);
            $data = array_merge($fieldsetConfig, $data);
        }

        $classGenerator = new ClassGenerator();
        $fieldsetClassName = (isset($data['alt-name'])?$data['alt-name']:$data['name']);
        $fieldsetClassName = $this->normaliseName($fieldsetClassName, true);
        $classGenerator->setName($fieldsetClassName);

        $tags = [];

        if (isset($data['name'])) {
            $tags['tags'][] = ['name'=> sprintf('Form\\Name("%s")', $data['name'])];
        }

        if (isset($data['type'])) {
            $tags['tags'][] = ['name'=> sprintf('Form\\Type("%s")', $data['type'])];
        }

        if (isset($data['attributes'])) {
            $tags['tags'][] =
                ['name' => sprintf("Form\\Attributes(%s)", $this->encodeOptionBlock($data['attributes']))];
        }

        if (isset($data['options'])) {
            $tags['tags'][] = ['name' => sprintf("Form\\Options(%s)", $this->encodeOptionBlock($data['options']))];
        }

        $classGenerator->setDocBlock(DocBlockGenerator::fromArray($tags));

        if (isset($data['elements'])) {
            foreach ($data['elements'] as $name => $element) {
                $element['name'] = isset($element['name'])? $element['name']: $name;
                $classGenerator->addPropertyFromGenerator($this->buildElement($element));
            }
        } else {
            $this->getLog()->warn('Empty Fieldset: ' .$data['name']);
        }

        $fieldsetName = $data['name'];

        unset(
            $data['elements'],
            $data['attributes'],
            $data['name'],
            $data['options'],
            $data['type'],
            $data['alt-name']
        );

        if (!empty($data)) {
            $this->getLog()->err('Unhandled properties for fieldset: '. json_encode(array_keys($data)));
        }

        $fileGenerator = new FileGenerator();
        $fileGenerator->setClass($classGenerator);
        $fileGenerator->setNamespace($this->namespace . '\Form\Model\Fieldset');
        $fileGenerator->setUse('Zend\Form\Annotation', 'Form');
        $fileGenerator->setFilename($this->basePath . 'Form/Model/Fieldset/' . $classGenerator->getName() . '.php');

        try {
            $this->write($fileGenerator);
        } catch (\Exception $e) {
            //fieldset with duplicate name but different definition
            $fieldsetClassName = $formName . $fieldsetClassName;
            $classGenerator->setName($fieldsetClassName);
            $fileGenerator->setFilename($this->basePath . 'Form/Model/Fieldset/' . $classGenerator->getName() . '.php');
            $this->write($fileGenerator);
        }

        return ['classname'=>$fieldsetClassName, 'name'=>$fieldsetName];

    }

    protected function normaliseName($name, $ucFirst = false)
    {
        $name = str_replace([' ', '_'], '-', $name);

        $filter = new DashToCamelCase();

        if (!$ucFirst) {
            return lcfirst($filter->filter($name));
        }

        return $filter->filter($name);
    }

    public function encodeOptionBlock($data)
    {
        $string = json_encode($data);
        if (strlen($string) > 75) {
            $string = json_encode($data, JSON_PRETTY_PRINT);
        }

        $string = stripslashes($string);
        $string = str_replace(['[', ']'], ['{', '}'], $string);

        return $string;
    }

    public function getLog()
    {
        if (is_null($this->log)) {
            $this->log = new Logger();

            $format = '%timestamp% %priorityName% (%priority%): %message%';
            $formatter = new \Zend\Log\Formatter\Simple($format);

            $writer = new Stream('php://stdout');
            $writer->setFormatter($formatter);
            $this->log->addWriter($writer);
        }

        return $this->log;

    }

    /**
     * @param $file
     */
    public function write($file)
    {
        if (file_exists($file->getFileName())) {
            if (file_get_contents($file->getFileName()) != $file->generate()) {
                $this->getLog()->info('Naming clash with file: ' . $file->getFileName());
                throw new \Exception();
            } else {
                $this->getLog()->info('Shared file: ' . $file->getFileName());
            }
        } else {
            $file->write();
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function updateElementWithTemplateSpec($data)
    {
        $config = $this->getServiceLocator()->get('Config');
        $elementSpec = $config['form']['elements'][$data['type']];
        $data['type'] = $elementSpec['type'];

        if (isset($elementSpec['attributes'])) {
            $data['attributes'] = $elementSpec['attributes'];
        }

        if (isset($elementSpec['options'])) {
            $data['options'] = $elementSpec['options'];
        }

        if (!empty($elementSpec['name'])) {
            $this->getLog()->warn('None empty name property in element spec: ' . $elementSpec['name']);
        }

        if (isset($elementSpec['filters'])) {
            $data['input_filters'] = $elementSpec['filters'];
        }

        if (isset($elementSpec['validators'])) {
            $data['validators'] = $elementSpec['validators'];
        }

        if (isset($elementSpec['required'])) {
            $data['required'] = $elementSpec['required'];
        }

        unset(
            $elementSpec['type'],
            $elementSpec['attributes'],
            $elementSpec['options'],
            $elementSpec['name'],
            $elementSpec['filters'],
            $elementSpec['validators'],
            $elementSpec['required']
        );
        if (!empty($elementSpec)) {
            $this->getLog()->err(
                'Unhandled properties for element spec: ' . $data['name'] . ' ' . json_encode(array_keys($elementSpec))
            );
        }

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function mutateElementType($data)
    {
        if (isset($data['filters'])) {
            $this->getLog()->info('Substituting type: ' . $data['type'] . ' for custom type: ' . $data['filters']);
            $data['type'] = $data['filters'];
        }

        if (strpos($data['type'], 'InputFilters') !== false) {
            $this->getLog()->info('Converting type: ' . $data['type'] . ' to build in type');
            $class = new $data['type'];
            if ($class instanceof InputProviderInterface) {
                $spec = $class->getInputSpecification();
                if (isset($spec['continue_if_empty']) && $spec['continue_if_empty']) {
                    $this->getLog()->warn('Continue if empty set in type: ' . $data['type']);
                    return $data;
                }

                if (strpos($data['type'], 'Text') !==false) {
                    $data['type'] = 'Text';

                    if ($class instanceof TextArea) {
                        $data['type'] = 'TextArea';
                    }
                } elseif (
                    strpos($data['type'], 'Vrm') !== false ||
                    strpos($data['type'], 'Name') !== false ||
                    strpos($data['type'], 'Phone') !== false ||
                    strpos($data['type'], 'CompanyNumber') !== false ||
                    strpos($data['type'], 'Email') !== false ||
                    strpos($data['type'], 'Gpw') !== false
                ) {
                    $data['type'] = 'Text';
                } elseif (strpos($data['type'], 'Hidden') !== false) {
                    $data['type'] = 'Hidden';
                } else {
                    $this->getLog()->warn('Failed converting type: ' . $data['type'] . ' to build in type');
                    return $data;
                }

                $data = $this->extractSpecFromElement($data, $class);
            }
        }

        return $data;
    }

    /**
     * @param $data
     * @param $class
     * @return mixed
     */
    protected function extractSpecFromElement(array $data, InputProviderInterface $class)
    {
        $spec = $class->getInputSpecification();
        if (isset($spec['required'])) {
            $data['required'] = $spec['required'];
        }

        if (isset($spec['filters'])) {
            $data['input_filters'] = $spec['filters'];
        }

        if (isset($spec['validators'])) {
            $data['validators'] = $spec['validators'];
        }

        if (isset($spec['allow_empty'])) {
            $data['allow_empty'] = $spec['allow_empty'];
        }

        if (isset($spec['continue_if_empty'])) {
            $data['continue_if_empty'] = $spec['continue_if_empty'];
        }

        unset(
            $spec['name'],
            $spec['required'],
            $spec['filters'],
            $spec['validators'],
            $spec['allow_empty'],
            $spec['continue_if_empty']
        );
        if (!empty($spec)) {
            $this->getLog()->err(
                'Unhandled properties for input spec: ' . $data['name'] . ' ' . json_encode(array_keys($spec))
            );
            return $data;
        }
        return $data;
    }

    public function cleanupAction()
    {
        $this->init();
        foreach (glob($this->basePath . 'Form/Model/Form/*.php') as $file) {
            unlink($file);
        }
        foreach (glob($this->basePath . 'Form/Model/Fieldset/*.php') as $file) {
            unlink($file);
        }
    }
}
