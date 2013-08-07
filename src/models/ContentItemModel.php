<?php namespace Stwt\Mothership;

use Page;
use Content;
use ContentRegion;
use Log;
use NavigationMenu;

class ContentItemModel extends BaseModel
{
    protected $table = "content_items";

    public function __toString()
    {
        if ($this->id) {
            if (!$this->content) {
                $this->content()->get();
            }
            return $this->content;
        }
        return parent::__toString();
    }

    // ----------------------------
    
    public function contentRegion()
    {
        $class = Config::get('mothership::models')['contentRegion'];
        Log::error('ContentItem belongsTo '.$class);
        return $this->belongsTo($class);
    }

    public function content()
    {
        return $this->morphTo();
    }

    public function navigationMenu()
    {
        return $this->morphTo();
    }

    // ----------------------------
    
    public function type()
    {
        if (!$this->content) {
            $this->content()->get();
        }

        $contentClass = Config::get('mothership::models')['content'];
        $navigationMenuClass = Config::get('mothership::models')['navigationMenu'];

        switch (get_class($this->content)) {
            case $contentClass:
                return $this->content->type();
                break;
            case $navigationMenuClass:
                return 'Navigation Menu';
                break;
        }
    }


    public function generate()
    {
        if (!$this->content) {
            $this->content()->get();
        }
        return $this->content->generate();
    }
}
