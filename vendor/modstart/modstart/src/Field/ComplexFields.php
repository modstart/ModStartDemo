<?php


namespace ModStart\Field;


use ModStart\Core\Util\SerializeUtil;


class ComplexFields extends AbstractField
{
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'fields' => [
                                                                                                            ],
            'iconServer' => modstart_admin_url('widget/icon'),
            'linkServer' => modstart_admin_url('widget/link_select'),
        ]);
    }

    private function getDefaultValues()
    {
        $fields = $this->getVariable('fields');
        $defaultValue = [];
        foreach ($fields as $f) {
            $defaultValue[$f['name']] = isset($f['defaultValue']) ? $f['defaultValue'] : null;
        }
        return $defaultValue;
    }

    public function iconServer($server)
    {
        $this->addVariables(['iconServer' => $server]);
        return $this;
    }

    public function linkServer($server)
    {
        $this->addVariables(['linkServer' => $server]);
        return $this;
    }

    public function fields($value)
    {
        $this->addVariables(['fields' => $value]);
        $this->defaultValue($this->getDefaultValues());
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = new \stdClass();
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = new \stdClass();
        }
        return $value;
    }
}
