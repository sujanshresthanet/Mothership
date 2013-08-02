<?php namespace Stwt\Mothership;

use Page;
use NavigationMenu;
use Attribute;

/**
 * An example of how to write code to PEAR's standards
 *
 * @category   
 * @package    
 * @copyright  
 * @license    
 * @version    
 * @link       https://github.com/th3hamburgler/Mothership
 */
class NavigationItemModel extends BaseModel
{
    /**
     * Name of the mysql table for this model
     * 
     * @var string
     */
    protected $table = "navigation_items";

    protected $plural = 'Items';
    protected $singular = 'Item';

    protected $fillable = [
        'title',
        'url',
        'type',
        'page_id',
        'navigation_menu_id',
    ];

    protected $columns = [
        'title',
        'type',
    ];

    public function __toString()
    {
        if ($this->id) {
            return $this->title;
        }
        return parent::__toString();
    }

    /**
     * --------------------------------------
     */
    
    public function navigationItems()
    {
        return $this->belongsTo('NavigationMenu');
    }

    public function page()
    {
        return $this->belongsTo('Page');
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
        switch ($this->type) {
            case 'page':
                return $this->generatePage();
                break;
            case 'url':
                return $this->generateURL();
                break;
        }
    }

    protected function generatePage()
    {
        $page = $this->page()->first();
        return '<li '.$this->generateAttributes().'><a href="'.$page->url().'">'.$this->title.'</a></li>';
    }

    protected function generateURL()
    {
        return '<li '.$this->generateAttributes().'><a href="'.$this->url.'">'.$this->title.'</a></li>';
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
