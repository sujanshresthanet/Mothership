<?php namespace Stwt\Mothership;

use Config;
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
        return $this->belongsTo('contentRegion');
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

        switch (get_class($this->content)) {
            case 'Content':
                return $this->content->type();
                break;
            case 'NavigationMenu':
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
