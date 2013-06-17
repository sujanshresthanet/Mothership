<?php namespace Stwt\Mothership;

use Cache;
use Config;
use DB;
use Eloquent;
use Log;
use Str;
use \Mockery as Mockery;
use \LaravelBook\Ardent\Ardent;

use Input;
use Validator;
use MessageBag;

class BaseModel extends Ardent
{
    protected $table;

    protected $singular;

    protected $plural;

    /**
     * Columns that are hidden when returning the model as an array or json
     * 
     * @var array
     */
    protected $hidden = [];

    /**
     * Columns that can't be "mass assigned", and will not appear in the default forms
     * 
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Do not keep a record of changes to these columns in the revisons table
     * 
     * @var array
     */
    protected $dontKeepRevisionOf = ['updated_at', 'created_at',];

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
    protected $properties = [];

    /**
     * Default columns that are displayed in the admin table
     * @var array
     */
    protected $columns = null;

    /**
     * Default fields that will appear in the admin form
     * @var array
     */
    protected $fields = null;
    
    /**
     * Declare all rules for fields in the model
     * 
     * @var array
     */
    public static $rules = [];

    /**
     * If set to true, models are hydrated from form input submission automatically.
     * We'll set this to true as and when we need it
     * 
     * @var boolean
     */
    public $autoHydrateEntityFromInput = false;
    public $autoPurgeRedundantAttributes = true;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->properties = $this->initProperties($this->properties);
        $this->loadColumns();
    }

    /**
     * Method used to make testing cleaner
     * 
     * @return Mockery
     */
    public static function shouldReceive()
    {
        $class = get_called_class();
        $repo = "Stwt\\Storage\\{$class}\\{$class}RepositoryInterface";
        $mock = Mockery::mock($repo);

        App::instance($repo, $mock);

        return call_user_func_array(
            [$mock, 'shouldReceive'],
            func_get_args()
        );
    }

    public function __toString()
    {
        if ($this->id) {
            return class_basename(get_class($this)).': '.$this->id;
        }
        return 'New '.$this->singular();
    }

    public function singular($uppercase = true)
    {
        $singular = ($this->singular ?: str_singular($this->table));
        return $uppercase ? ucwords($singular) : $singular;
    }

    public function plural($uppercase = true)
    {
        $plural = ($this->plural ?: str_plural($this->singular()));
        return $uppercase ? ucwords($plural) : $plural;
    }

    public function hasManyName()
    {
        return $this->table;
    }

    public function hasOneName()
    {
        return str_singular($this->table);
    }

    /**
     * Loads table column schema from the database
     * We cache this request to save database queries if cache
     * time is set in the mothership config file.
     *
     * @return   void
     */
    public function loadColumns()
    {
        $key = 'Mothership'.get_class($this).'Properties';
        $cacheTime = Config::get('mothership::cache');

        if ($cacheTime and Cache::has($key)) {
            $properties = Cache::get($key);
        } else {
            $properties = $this->loadColumnsFromDatabase();
            if ($cacheTime) {
                Cache::put($key, $properties, $cacheTime);
            }
        }
        $this->properties = $properties;
    }

    /**
     * Query the database to get all columns in the table, then
     * initialise a Field instance for each column.
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
            $properties[$name] = new Field($column, $this->table, $existing);
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
        $subset = ( $subset ?: array_diff(array_keys($this->properties), $this->guarded) );
        $properties = [];
        foreach ($subset as $k => $v) {
            if (is_callable($v)) {
                $properties[$k] = $v;
            } elseif (is_string($v) and isset($this->properties[$v])) {
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
        $fields = ($fields ?: array_diff(array_keys($this->properties), $this->guarded));
        $rules = [];
        if ($fields) {
            foreach ($fields as $k => $v) {
                if (is_string($v) and $this->hasProperty($v)) {
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
     * @return object Field
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

    /**
     * Validate the model instance Override Ardent method
     *
     * Add logic to the set attribute part so we do not assign empty string
     * to attributes that are nullable. These are set to null instead
     *
     * @param array   $rules          Validation rules
     * @param array   $customMessages Custom error messages
     * @return bool
     */
    public function validate(array $rules = array(), array $customMessages = array())
    {
        // check for overrides, then remove any empty rules
        $rules = (empty($rules)) ? static::$rules : $rules;
        foreach ($rules as $field => $rls) {
            if ($rls == '') {
                unset( $rules[$field] );
            }
        }

        if (empty($rules)) {
            return true;
        }

        $customMessages = (empty($customMessages)) ? static::$customMessages : $customMessages;

        if ($this->forceEntityHydrationFromInput || (empty($this->attributes) && $this->autoHydrateEntityFromInput)) {
            // pluck only the fields which are defined in the validation rule-set
            $attributes = array_intersect_key(Input::all(), $rules);

            // Set each given attribute on the model
            foreach ($attributes as $key => $value) {
                // check if "" posted and property is allows null values
                if ($this->getPropery($key)->allowsNull() and empty($value)) {
                    // set attribure to null
                    $this->setAttribute($key, null);
                } else {
                    $this->setAttribute($key, $value);
                }
            }
        }

        $data = $this->attributes; // the data under validation

        // perform validation
        $validator = Validator::make($data, $rules, $customMessages);
        $success = $validator->passes();

        if ($success) {
            // if the model is valid, unset old errors
            if ($this->validationErrors->count() > 0) {
                $this->validationErrors = new MessageBag;
            }
        } else {
            // otherwise set the new ones
            $this->validationErrors = $validator->messages();

            // stash the input to the current session
            if (Input::hasSessionStore()) {
                Input::flash();
            }
        }

        return $success;
    }


    protected function initProperties($properties)
    {
        return $properties;
    }
}
