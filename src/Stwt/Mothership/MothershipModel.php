<?php namespace Stwt\Mothership;

use Cache;
use Config;
use DB;
use Log;
use Stwt\Mothership\MothershipModelField as MothershipModelField;
use Str;
use Venturecraft\Revisionable\Revisionable;

class MothershipModel extends Revisionable
{

    protected $properties   = [];
    protected $hidden       = ['password'];

    protected $columns      = null;
    protected $fields       = null;

    protected $table;

    protected $dontKeepRevisionOf = [
        'updated_at',
        'created_at',
    ];

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
     * @return   string
     */
    public function __toString()
    {
        if ($this->id) {
            return get_class($this).': '.$this->id;
        }
        return null;
    }

    /**
     * Checks if this instance exists in the db
     *
     * @return boolean
     */
    public function exists()
    {
        return isset($this->id);
    }

    /**
     * Return a string representation of this instance for
     * use in the revision history
     *
     * @access   public
     * @return   string
     */
    public function identifiableName()
    {
        if ($this->id) {
            return $this->__toString();
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
     * We cache this request to save database queries if cache
     * is set to true in the mothership config file
     *
     * @return   void
     */
    public function loadColumns()
    {
        $key = 'Mothership'.get_class($this).'Properties';
        $loadFromCache = Config::get('mothership::cache');

        if ($loadFromCache AND Cache::has($key)) {
            $properties = Cache::get($key);
        } else {
            $properties = $this->loadColumnsFromDatabase();
            if ($loadFromCache) {
                Cache::forever($key, $properties);
            }
        }
        $this->properties = $properties;
    }

    /**
     * Query the database to get all columns in the table, then
     * initialise a MothershipModelField instance for each column.
     * This will automatically set the field type, validation rules ect.
     *
     * @return array
     */
    private function loadColumnsFromDatabase()
    {
        $columns = DB::select('show columns from '.$this->table);
        $properties = [];
        foreach ($columns as $column) {
            $name = $column->Field;
            $existing = (isset($this->properties[$name]) ? $this->properties[$name] : []);
            $properties[$name] = new MothershipModelField($column, $this->table, $existing);
        }
        return $properties;
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
            } else {
                $properties[$k] = $v;
            }
        }
        return $properties;
    }

    /**
     * Return an array of each fields validation rules.
     *
     * First parameter can contain an array of field names to
     * return rules just for that subset of fields.
     * e.g. ['first_name', 'last_name', 'email']
     *
     * Alternatively it can contain and array of validation rules 
     * for each field override the rules in the model. The key should
     * be the field name and value an array of rules.
     * e.g. ['first_name' => ['required', 'alpha']]
     *
     * $fields may contain a mixture of the above.
     * 
     * @param array $fields
     *
     * @return array
     */
    public function getRules($fields = [])
    {
        $fields = ($fields ?: array_keys($this->properties));
        $rules = [];
        if ($fields) {
            foreach ($fields as $k => $v) {
                Log::error($k.' '.$v);
                if (is_string($v) AND $this->hasProperty($v)) {
                    $rules[$v] = $this->getRule($v);
                } elseif (is_array($v)) {
                    $rules[$k] = $this->getRule($k, $v);
                }
            }
        }
        return $rules;
    }

    /**
     * Returns validation rules for a given property. Second parameter
     * can be passed to override model based rules.
     *
     * @paran string
     * @paran array
     *
     * @return array
     */
    public function getRule($property, $override = [])
    {
        $rules = ($override ?: $this->properties[$property]->validation);
        $filteredRules = [];
        // Filter rules, check for the unique rule and append column and id
        // details if found
        foreach ($rules as &$rule) {
            if (Str::startsWith($rule, 'unique')) {
                // get any params after 'unique:' in the rule
                $params   = explode(',', (substr($rule, 7) ?: ''));
                $table    = Arr::e($params, 0, $this->table);
                $column   = Arr::e($params, 1, $property);
                $except   = Arr::e($params, 2, $this->id);
                $idColumn = Arr::e($params, 3, 'id');
                
                $params = [$table, $column, $except, $idColumn];
                $rule = "unique:".implode(',', $params);
            }
        }
        return $rules;
    }

    /**
     * Checks if the model has a given property
     *
     * @return boolean
     */
    public function hasProperty($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * Adds a new rule to a property on this instance
     *
     * @param string $property
     * @param string $rule
     *
     * @return boolean
     */
    public function addRule($property, $rule)
    {
        if (!$this->hasProperty($property)) {
            return false;
        }
        $this->properties[$property]->validation[] = $rule;
        return true;
    }

    /**
     * Return a single property object from the class
     *
     * @param string $property
     *
     * @return object MothershipModelField
     */
    public function getPropery($property)
    {
        return $this->properties[$property];
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
        return ( isset($this->properties[$name]) ?: false );
    }
}
