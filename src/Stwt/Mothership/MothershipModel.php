<?php namespace Stwt\Mothership;

use DB;
use Eloquent;
use Log;
use Stwt\Mothership\MothershipModelField as MothershipModelField;

class MothershipModel extends Eloquent {

    protected $properties   = [];
    protected $columns      = null;
    protected $fields       = null;

    protected $table;

    public function __construct() {
        parent::__construct();
        $this->loadColumns();
    }

   /*
    * Mock the Repo Interface for this model to make testing cleaner
    * CODE SMELL - but Jeffery said it's ok!
    */
    public static function shouldReceive($value='')
    {
        $repo = get_called_class() . 'RepositoryInterface';
        $mock = Mockery::mock($repo);
        App::instance($repo, $mock);
        return call_user_func_array([$mock, 'shouldReceive'], func_get_args());
    }

   /**
    * Return a string representation of this instance
    *
    * @access   public
    * @param    void
    * @return   void
    */
    public function __toString() {
        if ( $this->id )
        {
            return $this->id;
        }
        return 'null';
    }

    public function plural($uppercase=true)
    {
        return ($uppercase ? ucwords($this->table) : $this->table);
    }

    public function singular($uppercase=true)
    {
        return trim(($uppercase ? ucwords($this->table) : $this->table), 's');
    }

   /**
    * Loads table column schema from the database
    *
    * @return   void
    */
    public function loadColumns() {
        Log::error('loadColumns');
        $columns = DB::select('show columns from '.$this->table);
        $properties = [];
        foreach ($columns as $column) {
            $name = $column->Field;
            Log::error('load '.$name);
            $existing = (isset($this->properties[$name]) ? $this->properties[$name] : []);
            Log::error(print_r($existing,1));
            $properties[$name] = new MothershipModelField($column, $this->table, $existing);
        }
        $this->properties = $properties;
    }

   /**
    * Return an array of columns in this table
    *
    * Pass an array of property names to return
    * a subset, else all properties in the db 
    * will be returned
    *
    * @param    array   $subset
    * @return   array
    */
    public function getColumns($subset=null)
    {
        return $this->getProperties($subset);
    }

   /**
    * Return an array of fields specifications
    *
    * Pass an array of property names to return
    * a subset, else all properties in the db 
    * will be returned
    *
    * @param    array   $subset
    * @return   array
    */
    public function getFields($subset=null)
    {
        return $this->getProperties($subset);
    }

    public function getProperties($subset=null)
    {
        if (!$subset)
        {
            return $this->properties;
        }
        $properties = [];
        foreach ($this->properties as $n => $v)
        {
            Log::error('check for '.$n);
            if (in_array($n, $subset))
            {
                $properties[$n] = $v;
            }
        }
        return $properties;
    }

   /**
    * Return an array of each fields validation rules
    *
    * @return   array
    */
    public function getRules() {
        $rules = [];
        foreach ($this->properties as $name => $property) {
            $rules[$name] = $property->validation;
        }
        return $rules;
    }
}