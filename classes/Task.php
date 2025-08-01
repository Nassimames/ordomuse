<?php
class Task
{
    public $id;
    public $title;
    public $description;
    public $is_done;
    public $created_at;
    public $updated_at;

    public function __construct($id, $title, $description, $is_done, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->is_done = $is_done;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
