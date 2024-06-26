<?php

namespace ModStart\Layout;

use Illuminate\Contracts\Support\Renderable;

class Row implements Buildable, Renderable
{
    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * @var Renderable[] flex columns
     */
    protected $flexColumns = [];

    /**
     * row classes.
     *
     * @var array
     */
    protected $class = [];

    public static function make($content)
    {
        $ins = new static($content);
        return $ins;
    }

    /**
     * Row constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        if (!empty($content)) {
            if ($content instanceof \Closure) {
                call_user_func($content, $this);
            } else {
                $this->column(12, $content);
            }
        }
    }

    /**
     * Add a column.
     *
     * @param int|array $width
     * @param $content
     */
    public function column($width, $content)
    {
        if (is_float($width)) {
            $width = $width < 1 ? round(12 * $width) : $width;
        }

        $column = new Column($content, $width);

        $this->addColumn($column);
    }

    public function flexColumn(Renderable $renderable)
    {
        $this->flexColumns[] = $renderable;
    }

    /**
     * @param Column $column
     */
    protected function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * Build row column.
     */
    public function build()
    {
        $this->startRow();
        foreach ($this->columns as $column) {
            $column->build();
        }
        if (!empty($this->flexColumns)) {
            echo "<div class='col-12 col-flex-container'>";
            foreach ($this->flexColumns as $column) {
                echo "<div class='col-flex-item'>";
                echo $column->render();
                echo "</div>";
            }
            echo "</div>";
        }
        $this->endRow();
    }

    /**
     * Start row.
     */
    protected function startRow()
    {
        $class = $this->class;
        $class[] = 'row';
        echo '<div class="' . implode(' ', $class) . '">';
    }

    /**
     * End column.
     */
    protected function endRow()
    {
        echo '</div>';
    }

    /**
     * Render row.
     *
     * @return string
     */
    public function render()
    {
        ob_start();

        $this->build();

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
}
