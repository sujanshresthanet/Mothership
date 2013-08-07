<?php namespace Stwt\Mothership;

use Config;
use View;

class TemplateModel
{
    public $globalRegions = [];
    public $regions = [];
    public $name;

    /**
     * Load the template data from config/cache
     * 
     * @param  string $name template name
     * 
     * @return object
     */
    public static function get($name)
    {
        $class = get_called_class();
        $template = new $class;
        $template->name = $name;
        $template->globalRegions = Config::get('templates.globalRegions');
        $template->regions = Config::get('templates.templates.'.$name.'.regions');
        return $template;
    }

    /**
     * Return true if the view file exists for this template
     * 
     * @return boolean
     */
    public function exists()
    {
        return View::exists($this->path());
    }

    /**
     * Return the view path for this template
     * 
     * @return string
     */
    public function path()
    {
        return 'public.templates.'.$this->name;
    }

    /**
     * Return key's for all unique regions that appear in this template
     * 
     * @return array
     */
    public function pageRegions()
    {
        return $this->regions;
    }

    /**
     * Return key's for all global regions that appear in this template
     * 
     * @return array
     */
    public function globalRegions()
    {
        return $this->globalRegions;
    }

    /**
     * Return key's for all regions that appear in this template
     * 
     * @return array
     */
    public function regions()
    {
        return array_merge($this->globalRegions(), $this->pageRegions());
    }
}
