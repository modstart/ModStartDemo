<?php


namespace ModStart\Support\Concern;



trait HasVariables
{
    protected $variables = [];

    public function varaibles()
    {
        return $this->variables;
    }

    
    public function setVariable($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->variables[$k] = $v;
            }
        } else {
            $this->variables[$name] = $value;
        }
    }

    
    public function getVariable($name, $default = null)
    {
        return isset($this->variables[$name]) ? $this->variables[$name] : $default;
    }
}
