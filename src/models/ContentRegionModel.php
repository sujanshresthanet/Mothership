<?php namespace Stwt\Mothership;

use Config;
use Log;
use Str;

class ContentRegionModel extends BaseModel
{
    protected $table = "content_regions";

    /**
     * This models db column properties
     * ---
     * This array is auto loaded from the database details but any of the
     * attributes can be overridden here.
     * 
     * Example of column properties:
     * 
     * [column_name] => [
     *     'label'      => '',  // the column label
     *     'form'       => '',  // the type of form element e.g. [input, select, textarea]
     *     'validation' => [],  // array of validation rules
     * ],
     * 
     * @var array
     */
    protected $properties = [
        'shared' => [
            'help' => 'Tick this box if the regions content will be shared amongst pages',
        ],
    ];

    /**
     * Default columns that are displayed in the admin table
     * @var array
     */
    protected $columns = [];

    protected $singular = 'Region';

    protected $plural = 'Regions';

    public function __toString()
    {
        if ($this->id) {
            return $this->key;
        }
        return parent::__toString();
    }

    public function initColumns($columns)
    {
        return [
            'Key' => function ($region) {
                return ucwords(humanize($region->key));
            },
            'Type' => function ($region) {
                return $region->type();
            },
            'Page' => function ($region) {
                if (!$region->page) {
                    $region->page()->first();
                }
                if (!$region->page) {
                    return 'Shared';
                } else {
                    return $region->page->name;
                }
            }
        ];
    }

    // ----------------------------
    
    public function page()
    {
        $class = Config::get('mothership::models')['page'];
        return $this->belongsTo($class);
    }

    public function contentItems()
    {
        $class = Config::get('mothership::models')['contentItem'];
        Log::error('ContentRegion hasMany '.$class);
        return $this->hasMany($class);
    }

    // ----------------------------
    
    public function isShared()
    {
        return (!$this->page);
    }

    public function type()
    {
        if (!$this->contentItems) {
            $this->contentItems()->get();
        }
        return $this->contentItems[0]->type();
    }

    public function excerpt()
    {
        return Str::limit(strip_tags($this->generate()), 100);
    }

    /**
     * Build the html of associated items/contents
     * 
     * @return string
     */
    public function generate()
    {
        $html = '';
        foreach ($this->contentItems as $item) {
            $html .= $item->generate();
        }
        return $html;
    }
}
