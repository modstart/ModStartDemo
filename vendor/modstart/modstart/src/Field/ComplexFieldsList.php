<?php


namespace ModStart\Field;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;


class ComplexFieldsList extends AbstractField
{
    protected $value = [];
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'fields' => [
                                                                                                                                                                            ],
            'valueItem' => new \stdClass(),
            'iconServer' => modstart_admin_url('widget/icon'),
            'iconGroups' => ['iconfont', 'font-awesome'],
            'linkServer' => modstart_admin_url('widget/link_select'),
            '_hasIcon' => false,
        ]);
    }

    private function getValueItem()
    {
        $fields = $this->getVariable('fields');
        $valueItem = [];
        foreach ($fields as $f) {
            $valueItem[$f['name']] = isset($f['defaultValue']) ? $f['defaultValue'] : null;
        }
        if (empty($valueItem)) {
            $valueItem = new \stdClass();
        }
        return $valueItem;
    }

    public function iconServer($server)
    {
        $this->addVariables(['iconServer' => $server]);
        return $this;
    }

    
    public function iconGroups($iconGroups)
    {
        $this->addVariables(['iconGroups' => $iconGroups]);
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
        $this->addVariables(['valueItem' => $this->getValueItem()]);
        $nameMap = [];
        foreach ($value as $f) {
            BizException::throwsIf('ComplexFieldsList.字段名重复 - ' . $f['name'], isset($nameMap[$f['name']]));
            $nameMap[$f['name']] = true;
            if ($f['type'] == 'icon') {
                $this->addVariables(['_hasIcon' => true]);
            }
        }
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
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
            $value = [];
        }
        return $value;
    }
}
