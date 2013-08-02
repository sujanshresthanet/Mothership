<?php namespace Stwt\Mothership;

use ContentItem;
use NavigationItem;
use Attribute;

class NavigationMenuModel extends BaseModel
{
    /**
     * Name of the mysql table for this model
     * 
     * @var string
     */
    protected $table = "navigation_menus";

    protected $plural = 'Nav Menus';
    protected $singular = 'Nav Menu';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function __toString()
    {
        if ($this->id) {
            return $this->name;
        }
        return parent::__toString();
    }

    /**
     * --------------------------------------
     */

    public function contentItems()
    {
        return $this->morphMany('ContentItem', 'content');
    }

    public function navigationItems()
    {
        return $this->hasMany('NavigationItem');
    }

    public function attributes()
    {
        return $this->morphMany('Attribute', 'attributeable');
    }

    /**
     * --------------------------------------
     */
    
    public function generate()
    {
        $attributes = $this->generateAttributes();

        $items = [];
        foreach ($this->navigationItems as $item) {
            $items[] = $item->generate();
        }
        return "<ul $attributes>".implode(' ', $items)."</ul>";
    }

    public function generateAttributes()
    {
        $attributes = [];
        foreach ($this->attributes()->get() as $a) {
            $attributes[] = $a->generate();
        }
        return implode(' ', $attributes);
    }
}
