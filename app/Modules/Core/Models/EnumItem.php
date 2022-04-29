<?php

namespace App\Modules\Core\Models;

class EnumItem
{
    public string $id;

    public string $title;

    /**
     * @param string $id
     * @return EnumItem
     */
    public function setId(string $id): EnumItem
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $title
     * @return EnumItem
     */
    public function setTitle(string $title): EnumItem
    {
        $this->title = $title;
        return $this;
    }
}
