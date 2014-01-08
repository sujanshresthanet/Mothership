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

    protected $fields = [];

    protected $properties = [
        'type' => [
            'form'       => 'input',
            'type'       => 'radio',
        ],
    ];

    public function __toString()
    {
        if ($this->id) {
            return $this->title;
        }
        return parent::__toString();
    }

    // ------------------------- //
    // Eloquent Relationships    //
    // ------------------------- //
    
    public function navigationItems()
    {
        App::abort(501, 'Please define NavigationMenu relationship in you NavigationItem Model');
        return $this->belongsTo('NavigationMenu');
    }

    public function page()
    {
        App::abort(501, 'Please define Page relationship in you NavigationItem Model');
        return $this->belongsTo('Page');
    }

    public function attributes()
    {
        App::abort(501, 'Please define Attribute relationship in you NavigationItem Model');
        return $this->morphMany('Attribute', 'attributeable');
    }

    // ----------------------------
    
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
        $attributes = [];
        $page = $this->page()->first();
        $url = $page->url();

        if(\Request::url() == $url) {
            $attributes['class'] = 'active';
        }

        $attributes = $this->generateAttributes($attributes);

        return '<li '.$attributes.'><a href="'.$url.'">'.$this->title.'</a></li>';
    }

    protected function generateURL()
    {
        return '<li '.$this->generateAttributes().'><a href="'.$this->url.'">'.$this->title.'</a></li>';
    }

    public function generateAttributes($attributes = [])
    {
        foreach ($this->attributes()->get() as $a) {
            if (isset($attributes[$a->key])) {
                $attributes[$a->key] = $attributes[$a->key].' '.$a->value;
            } else {
                $attributes[$a->key] = $a->value;
            }
        }

        $_attributes = [];
        foreach ($attributes as $k => $v) {
            $_attributes[] = "$k=\"$v\"";
        }

        return implode(' ', $_attributes);
    }
}
