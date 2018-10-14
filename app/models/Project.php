<?php
namespace App\Models;
class Project extends BaseElement{
    public function getDurationAsString() {
        $years = floor($this->months / 12);
        $extraMonths = $this->months % 12;
      
        return "Project duration: $years years $extraMonths months";
    }
}