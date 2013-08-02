<?php namespace Stwt\Mothership;

class AttributeModel extends BaseModel
{
    /**
     * Name of the mysql table for this model
     * 
     * @var string
     */
    protected $table = "attributes";

    public function attributeable()
    {
        return $this->morphTo();
    }

    public function generate()
    {
        return $this->key.'="'.$this->value.'"';
    }
}
