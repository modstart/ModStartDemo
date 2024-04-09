<?php


namespace ModStart\Field;


use ModStart\Core\Exception\BizException;

class Currency extends AbstractField
{
    protected $view = 'modstart::core.field.number';
    protected $rules = ['regex:/^\\d+(\\.\\d+)?$/i'];

    protected function setup()
    {
        $this->addVariables([
                        'autoColor' => false,
                        'signShow' => false,
                        'min' => null,
                        'max' => null,
                        'step' => null,
        ]);
    }

    public function min($value)
    {
        $this->setVariable('min', $value);
        return $this;
    }

    public function max($value)
    {
        $this->setVariable('max', $value);
        return $this;
    }

    public function step($value)
    {
        $this->setVariable('step', $value);
        return $this;
    }

    public function autoColor($value)
    {
        $this->setVariable('autoColor', $value);
        return $this;
    }

    public function signShow($value)
    {
        $this->setVariable('signShow', $value);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        return $value;
    }

    public function prepareInput($value, $model)
    {
        if ('' === $value || null === $value) {
            return null;
        }
        if (null !== ($v = $this->getVariable('min'))) {
            if ($value < $v) {
                BizException::throws(str_replace([
                    ':attribute', ':min'
                ], [
                    $this->label, $v
                ], L('validation.min.numeric')));
            }
        }
        if (null !== ($v = $this->getVariable('max'))) {
            if ($value > $v) {
                BizException::throws(str_replace([
                    ':attribute', ':max'
                ], [
                    $this->label, $v
                ], L('validation.max.numeric')));
            }
        }
        return $value;
    }
}
