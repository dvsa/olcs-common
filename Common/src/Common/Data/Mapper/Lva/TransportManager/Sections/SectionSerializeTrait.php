<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


trait SectionSerializeTrait
{


    public function sectionSerialize()
    {
        $templatePrefix = $this->getTranslationTemplate();

        $properties = array_combine(
            array_map(function ($k) use ($templatePrefix) {
                return $templatePrefix . $k;
            },
                array_keys(get_object_vars($this))),
            get_object_vars($this)
        );
        return $properties;
    }
}
