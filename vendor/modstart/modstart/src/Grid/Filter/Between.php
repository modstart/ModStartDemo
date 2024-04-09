<?php

namespace ModStart\Grid\Filter;

use ModStart\Core\Util\SerializeUtil;

class Between extends AbstractFilter
{
    
    protected $view = null;

    
    public function formatId($column)
    {
        $id = str_replace('.', '_', $column);

        return ['start' => "{$id}_start", 'end' => "{$id}_end"];
    }

    
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if (count($columns) == 1) {
            $name = $columns[0];
        } else {
            $name = array_shift($columns);

            foreach ($columns as $column) {
                $name .= "[$column]";
            }
        }

        return ['start' => "{$name}[start]", 'end' => "{$name}[end]"];
    }

    

    public function datetime($options = [])
    {
        $this->view = 'admin::filter.betweenDatetime';

        $this->prepareForDatetime($options);
    }

    protected function prepareForDatetime($options = [])
    {
        $options['format'] = array_get($options, 'format', 'YYYY-MM-DD HH:mm:ss');
        $options['locale'] = array_get($options, 'locale', config('app.locale'));

        $startOptions = SerializeUtil::jsonEncode($options);
        $endOptions = SerializeUtil::jsonEncode($options + ['useCurrent' => false]);
    }

    public function render()
    {
        if (isset($this->view)) {
            return view($this->view, $this->variables());
        }

        return parent::render();
    }
}
