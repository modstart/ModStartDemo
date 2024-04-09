<?php


namespace ModStart\Support\Concern;

use Illuminate\Support\Collection;
use ModStart\Field\AbstractField;
use ModStart\Field\Type\FieldRenderMode;


trait HasFields
{
    
    private $fields;
    
    private $fieldDefaultRenderMode = 'add';

    private function setupFields()
    {
        $this->fields = new Collection();
    }

    
    public function fillFields()
    {
        $this->fields()->each(function (AbstractField $field) {
            $field->fill($this->item);
        });
    }

    
    public function pushField(AbstractField $field)
    {
        $this->fields()->push($field);
        return $this;
    }

    
    public function removeField($column)
    {
        $this->fields = $this->fields()->filter(function (AbstractField $field) use ($column) {
            return $field->column() != $column;
        });
        return $this;
    }

    
    public function prependField(AbstractField $field)
    {
        $this->fields()->prepend($field);
        return $this;
    }

    public function fieldDefaultRenderMode($value = null)
    {
        if (null === $value) {
            return $this->fieldDefaultRenderMode;
        }
        return $this->fieldDefaultRenderMode = $value;
    }

    
    public function fields()
    {
        return $this->fields;
    }

    
    public function listableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->listable();
        });
    }

    
    public function addableFields($includeShowOnly = false)
    {
        return $this->fields->filter(function (AbstractField $item) use ($includeShowOnly) {
            return $item->addable() || ($includeShowOnly && $item->formShowOnly());
        });
    }

    
    protected function editableFields($includeShowOnly = false)
    {
        return $this->fields->filter(function (AbstractField $item) use ($includeShowOnly) {
            return $item->editable() || ($includeShowOnly && $item->formShowOnly());
        });
    }

    
    protected function showableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->showable();
        });
    }

    
    public function sortableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->sortable();
        });
    }

    
    public function getFieldByName($name)
    {
        return $this->fields->first(function ($k, AbstractField $item) use ($name) {
            return $item->name() == $name;
        });
    }

    
    public function getFieldByColumn($column)
    {
        return $this->fields->first(function ($k, AbstractField $item) use ($column) {
            return $item->column() == $column;
        });
    }

}
