<?php
/**
 * @author Michael Kyndt
 */

class ReportingBlockLayout
{
    private $width,$height;

    public function ReportingBlockLayout($w,$h)
    {
        $this->width = $w;
        $this->height = $h;
    }

    public function get_width()
    {
        return $this->width;
    }

    public function set_width($w)
    {
        $this->width = $w;
    }

    public function get_height()
    {
        return $this->height;
    }

    public function set_height($h)
    {
        $this->height = $h;
    }
}//ReportingBlockLayout
?>
