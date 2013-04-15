<?php namespace Stwt\Mothership;

use DB;
use Venturecraft\Revisionable\Revisionable;
use Stwt\Mothership\MothershipModelField as MothershipModelField;

class MothershipModel extends Revisionable
{

    protected $properties   = [];
    protected $hidden       = ['password'];

    protected $columns      = null;
    protected $fields       = null;

    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->loadColumns();
    }

    /*
     * Mock the Repo Interface for this model to make testing cleaner
     * CODE SMELL - but Jeffery said it's ok!
     */
    public static function shouldReceive($value = '')
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
    public function __toString()
    {
        if ($this->id) {
            return $this->id;
        }
        return 'null';
    }

    public function plural($uppercase = true)
    {
        return ($uppercase ? ucwords($this->table) : $this->table);
    }

    public function singular($uppercase = true)
    {
        return trim(($uppercase ? ucwords($this->table) : $this->table), 's');
    }

    /**
     * Loads table column schema from the database
     *
     * @return   void
     */
    public function loadColumns()
    {
        $columns = DB::select('show columns from '.$this->table);
        $properties = [];
        foreach ($columns as $column) {
            $name = $column->Field;
            $existing = (isset($this->properties[$name]) ? $this->properties[$name] : []);
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
    public function getColumns($subset = null)
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
    public function getFields($subset = null)
    {
        return $this->getProperties($subset);
    }

    /*
     * Returns an array of property objects. Define property
     * keys in $subset to return a selection of objects.
     *
     * If $subset is null all properties will be returned 
     * _except_ those in the models $hidden array.
     *
     * @param    array   $subset
     * @return   array
     */
    public function getProperties($subset = null)
    {
        $subset = ( $subset ?: array_diff(array_keys($this->properties), $this->hidden) );
        $properties = [];
        foreach ($subset as $k => $v) {
            if (is_callable($v)) {
                $properties[$k] = $v;
            } elseif (is_string($v) AND isset($this->properties[$v])) {
                $properties[$v] = $this->properties[$v];
            }
        }
        return $properties;
    }

    /**
     * Return an array of each fields validation rules
     *
     * @return   array
     */
    public function getRules()
    {
        $rules = [];
        foreach ($this->properties as $name => $property) {
            $rules[$name] = $property->validation;
        }
        return $rules;
    }

    /*
     * Returns true if $name is a database property
     * in this model
     *
     * @param    string  $name
     * @return   boolean
     */
    public function isProperty($name)
    {
        return ( isset($properties) ?: false );
    }
}
