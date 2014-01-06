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

    // ------------------------- //
    // Eloquent Relationships    //
    // ------------------------- //
    
    public function contentRegion()
    {
        App::abort(501, 'Please define contentRegion relationship in you ContentItem Model');
        return $this->belongsTo('ContentRegion');
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

        switch (class_basename(get_class($this->content))) {
            case 'Content':
                return $this->content->type();
                break;
            case 'NavigationMenu':
                return 'Navigation Menu';
                break;
            default:
                return class_basename(get_class($this->content));
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
