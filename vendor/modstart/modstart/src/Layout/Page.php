<?php

namespace ModStart\Layout;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use ModStart\Core\Input\Request;
use ModStart\Form\Form;
use ModStart\Grid\Grid;

class Page implements Renderable
{
    private $pageTitle = ' ';
    private $pageKeywords = '';
    private $pageDescription = '';
    private $rows = [];
    protected $view;
    private $viewData = [];

    public function __construct(\Closure $callback = null)
    {
        if ($callback instanceof Closure) {
            $callback($this);
        }
    }

    public function body($content)
    {
        return $this->row($content);
    }

    
    public function row($content)
    {
        if ($content instanceof Closure) {
            $row = new Row();
            call_user_func($content, $row);
            $this->addRow($row);
        } else {
            $this->addRow(new Row($content));
        }
        return $this;
    }

    public function append($content)
    {
        $this->rows[] = $content;
        return $this;
    }

    
    public function view($view = null)
    {
        if (null === $view) {
            return $this->view;
        }
        $this->view = $view;
        return $this;
    }

    public function putViewData($keyOrData, $value = null)
    {
        if (is_array($keyOrData)) {
            $this->viewData = array_merge($this->viewData, $keyOrData);
        } else {
            $this->viewData[$keyOrData] = $value;
        }
        return $this;
    }

    public function pageTitle($pageTitle = null)
    {
        if (null === $pageTitle) {
            return $this->pageTitle;
        }
        $this->pageTitle = $pageTitle;
        return $this;
    }

    public function pageKeywords($pageKeywords = null)
    {
        if (null === $pageKeywords) {
            if (empty($this->pageKeywords)) {
                return $this->pageTitle();
            }
            return $this->pageKeywords;
        }
        $this->pageKeywords = $pageKeywords;
        return $this;
    }


    public function pageDescription($pageDescription = null)
    {
        if (null === $pageDescription) {
            if (empty($this->pageDescription)) {
                return $this->pageTitle();
            }
            return $this->pageDescription;
        }
        $this->pageDescription = $pageDescription;
        return $this;
    }


    
    private function addRow(Row $row)
    {
        $this->rows[] = $row;
    }

    
    public function build()
    {
        ob_start();

        foreach ($this->rows as $row) {
            if ($row instanceof Buildable) {
                $row->build();
            } else {
                echo $row;
            }
        }

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    
    public function handleGrid($grid)
    {
        if (Request::isPost()) {
            return $grid->request();
        }
        return $this;
    }

    
    public function handleForm($form, $callback, array $data = null)
    {
        if (Request::isPost()) {
            return $form->formRequest($callback, $data);
        }
        return $this;
    }

    
    public function render()
    {
        $data = [
            'pageTitle' => $this->pageTitle(),
            'pageKeywords' => $this->pageKeywords(),
            'pageDescription' => $this->pageDescription(),
            'content' => $this->build(),
        ];
        return view($this->view(), array_merge($data, $this->viewData))->render();
    }
}
