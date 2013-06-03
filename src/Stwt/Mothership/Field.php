<?php namespace Stwt\Mothership;

use Log;
use Str;

/**
 * Represents an Model field. Attempts to map the properties found
 * in the database column
 */
class Field
{
    public $name;
    public $null;
    public $default;
    public $key;
    public $extra;
    public $form;
    public $label;
    public $type;
    public $validation = [];
    public $tooltip;
    public $options;
    public $unsigned;
    public $model;

    /*
     * The parent model instance
     */
    protected $parent;
    protected $table;
    protected $dataType;

    /**
     * Create a new Field Specification from a sql row
     *
     * @param object $row
     * @param string $table
     * @param array  $spec
     *
     * @return void
     */
    public function __construct($row = null, $table = null, $spec = [])
    {
        if ($row) {
            foreach ($spec as $k => $v) {
                $this->$k = $v;
            }
            $this->table    = $table;

            $this->name     = $row->Field;
            $this->dataType = $row->Type;
            $this->null     = ($row->Null == 'YES' ? true : false);
            $this->key      = $row->Key;
            $this->default  = $row->Default;
            $this->extra    = $row->Extra;

            $this->init($spec);
        }
    }

    /**
     * Initialise the db column depending on it's type
     *
     * @access   public
     * @return   void
     */
    public function init($spec)
    {
        $type = $this->dataType;
        $this->initField($spec);
        if (static::startsWith($type, 'int')) {
            $this->initInt();
        } elseif (static::startsWith($type, 'varchar')) {
            $this->initVarchar();
        } elseif (static::startsWith($type, 'float')) {
            $this->initFloat();
        } elseif (static::startsWith($type, 'decimal')) {
            $this->initDecimal();
        } elseif (static::startsWith($type, 'tinyint')) {
            $this->initTinyint();
        } elseif (static::startsWith($type, 'datetime')) {
            $this->initDatetime();
        } elseif (static::startsWith($type, 'date')) {
            $this->initDate();
        } elseif (static::startsWith($type, 'timestamp')) {
            $this->initDatetime();
        } elseif (static::startsWith($type, 'time')) {
            $this->initTime();
        } elseif (static::startsWith($type, 'enum')) {
            $this->initEnum();
        } elseif (static::startsWith($type, 'text')) {
            $this->initText();
        } elseif (static::startsWith($type, 'blob')) {
            $this->initText();
        } else {
            Log::error($type.' not initialised');
        }
    }

    /**
     * Set the fields default rules
     *
     * @access   public
     * @param    array
     * @return   void
     */
    public function initField($spec = [])
    {
        foreach ($spec as $k => $v) {
            $this->$k = $v;
        }
        if ($this->key == 'PRI') {
            $this->type = 'hidden';
        }
        if ($this->key == 'PRI' or $this->key == 'UNI') {
            $this->validation[] = 'unique:'.$this->table;
        }

        if ($this->isRequiredField()) {
            $this->validation[] = 'required';
        }
        $this->setLabel();
    }

    /**
     * Set the fields label if not already defined
     *
     * @return   void
     */
    public function setLabel()
    {
        if ($this->label) {
            return;
        }
        $this->label = static::humanize($this->name);
    }

    /**
     * This method attempts to guess if this field is
     * required. If this method returns true a 'required'
     * rule will be added to the fields validation rules.
     *
     * A Field will be treated as required if the database
     * column does not allow 'null' values.
     *
     * There are a few exceptions to this rule:
     * - The field is the tables primary key e.g. 'the id'
     * - The field has a default value
     *
     * @return boolean
     */
    protected function isRequiredField()
    {
        if ($this->key === 'PRI') {
            return false;
        }
        if ($this->default) {
            return false;
        }
        if ($this->null === false) {
            return true;
        }
    }

    /**
     * Initialise a integer column
     *
     * - check if it's unsigned
     * - set the max and min values
     * - add any validation rules
     * - check if the integer is a secondary key to a related model
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initInt()
    {
        $type = $this->dataType;
        // set default type
        if (!$this->type) {
            $this->type = 'number';
        }
        // check if unsigned
        if (Str::endsWith($type, 'unsigned')) {
            $this->unsigned = true;
            $type = str_replace(' unsigned', '', $type);
        }
        $length = $this->getConstraint($type);
        $max    = str_repeat('9', $length);
        $this->validation[] = 'max:'.$max;
        $this->validation[] = 'min:'.($this->unsigned ? 0 : '-'.$max);
        $this->validation[] = 'integer';
        $this->max          = $max;
        $this->min          = ($this->unsigned ? 0 : -$max);
        $this->step         = 1;

        // check if this integer is a foreign key to a valid object
        if (Str::endsWith($this->name, '_id')) {
            $relatedModel = Str::studly(substr($this->name, 0, strlen($this->name) - 3));
            if (class_exists($relatedModel) and is_subclass_of($relatedModel, 'Stwt\Mothership\BaseModel')) {

                $this->form = $this->form ?: 'select';
                $this->options = [];
                $this->model = $relatedModel;
                if (Str::endsWith($this->label, ' Id')) {
                    $this->label = substr($this->label, 0, strlen($this->label) - 3);
                }
            }
        }
    }

    /**
     * Initialise a integer column
     *
     * - set the max and min values
     * - add any validation rules
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initFloat()
    {
        $type = $this->dataType;
        // set default type
        if (!$this->type) {
            $this->type = 'number';
        }

        $length = $this->getConstraint($type);
        list($digits, $decimal) = explode(',', $length);
        
        $max = str_repeat('9', $digits-$decimal);
        $this->validation[] = 'numeric';
        $this->validation[] = 'max:'.$max;
        $this->validation[] = 'min:'.($this->unsigned ? 0 : '-'.$max);
        $max .= '.'.str_repeat('9', $decimal);
        $this->max          = $max;
        $this->min          = ($this->unsigned ? 0 : -$max);
        $this->step         = '.'.str_repeat('0', ($decimal-1)).'1';
    }

    /**
     * Initialise a decimal column
     *
     * - set the max and min values
     * - add any validation rules
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initDecimal()
    {
        $type = $this->dataType;
        // set default type
        if (!$this->type) {
            $this->type = 'number';
        }
        $length = $this->getConstraint($type);
        list($digits, $decimal) = explode(',', $length);
        
        $max = str_repeat('9', $digits-$decimal);
        $this->validation[] = 'numeric';
        $this->validation[] = 'max:'.$max;
        $this->validation[] = 'min:'.($this->unsigned ? 0 : '-'.$max);
        $max .= '.'.str_repeat('9', $decimal);
        $this->max          = $max;
        $this->min          = ($this->unsigned ? 0 : -$max);
        $this->step         = '.'.str_repeat('0', ($decimal-1)).'1';
    }

    /**
     * Initialise a tinyint column
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initTinyint()
    {
        $type = $this->dataType;
        if (!$this->type) {
            $this->type = 'checkbox_bool';
        }
    }

    /**
     * Initialise a datetime column
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initDatetime()
    {
        $type = $this->dataType;
        if (in_array($this->name, ['created_at', 'updated_at'])) {
            $this->type = 'hidden';
        } elseif (!$this->type) {
            $this->type = 'datetime';
            //$this->class = 'datetime';
        }
        $this->validation[] = 'date_format:Y-m-d H:i:s';
    }

    /**
     * Initialise a date column
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initDate()
    {
        $type = $this->dataType;
        if (!$this->type) {
            $this->type = 'date';
        }
        $this->validation[] = 'date_format:Y-m-d';
    }

    /**
     * Initialise a date column
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initTime()
    {
        $type = $this->dataType;
        if (!$this->type) {
            $this->type = 'time';
        }
        $this->validation[] = 'date_format:H:i:s';
    }

    /**
     * Initialise a varchar column
     *
     * - set the max length
     * - add any validation rules
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initVarchar()
    {
        $type = $this->dataType;
        // set default type
        if (!$this->type) {
            $this->type = 'text';
        }
        $length             = $this->getConstraint($type);
        $this->validation[] = 'max:'.$length;
    }

    /**
     * Initialise an enum column
     *
     * - set the field options
     * - set validation rules
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initEnum()
    {
        $type = $this->dataType;
        // set default type
        if (!$this->form) {
            $this->form = 'select';
        }
        $options            = $this->getConstraint($type);
        $optionString       = str_replace('\'', '', $options);
        $this->validation[] = 'in:'.$optionString;
        $options            = explode(',', $optionString);
        $labels             = [];
        foreach ($options as $o) {
            $labels[] = static::humanize($o);
        }
        $options        = array_combine($labels, $options);
        $this->options = ($this->null ? array_merge(['-- None --' => null], $options) : $options);
    }

    /**
     * Initialise a text column
     *
     * @access   private
     * @param    void
     * @return   void
     */
    private function initText()
    {
        $type = $this->dataType;
        // set default type
        if (!$this->form) {
            $this->form = 'textarea';
        }
    }

    /**
     * Returns the $instance value for this field
     * formatted for a table
     *
     * @param object $instance
     *
     * @return string
     */
    public function getTable($instance)
    {
        if ($this->model) {
            return $instance->{$this->model};
        } elseif ($this->isDate()) {
            $date = new \ExpressiveDate($instance->{$this->name});
            return $date->getRelativeDate();
        }
        return $instance->{$this->name};
    }

    /**
     * Return constraint defined in the column
     *
     * @access   private
     * @param    string
     * @return   int
     */
    private function getConstraint($type)
    {
        $constraint;
        preg_match_all('/\(([A-Za-z0-9,\' ]+?)\)/', $type, $constraint);
        return current(end($constraint));
    }

    /**
     * Extract the type of form field this column will user
     * from the SQL column type.
     *
     * @return   void
     */
    public function setFormDepreciated()
    {
        if ($this->type) {
            return;
        }
        $type = current(explode("(", $this->dataType));
        if ($this->key == 'PRI') {
            $this->type = 'hidden';
            return;
        }
        switch ($type) {
            case 'date':
                $this->type = 'date';
                break;
            case 'int':
                $this->type = 'number';
                break;
            case 'varchar':
                if ($this->options) {
                    $this->type = 'select';
                } else {
                    $this->type = 'text';
                }
                break;
            case 'text':
                $this->type = 'textarea';
                break;
            case 'timestamp':
                $this->type = 'datetime';
                break;
            default:
                $this->type = 'none';
                break;
        }
    }

    /**
     * Read only access to fields sql type
     *
     * @access   public
     * @return   string
     */
    public function dataType()
    {
        return $this->dataType;
    }

    /**
     * Returns true if the type of this field is a 
     * scalar type e.g. a number
     *
     * @access   protected
     * @return   boolean
     */
    protected function isScalar()
    {
        return in_array($this->type, ['number', 'double', 'integer']);
    }

    protected function isDate()
    {
        return in_array($this->dataType, ['date', 'datetime', 'timestamp']);
    }

    /**
     * Returns true if string starts with another string
     *
     * @access   protected
     * @param    string
     * @param    string
     * @return   boolean
     */
    protected static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    /**
     * takes an underscored string and humanizes it
     *
     * @access   protected
     * @param    string
     * @return   string
     */
    protected function humanize($string)
    {
        return  ucwords(str_replace('_', ' ', $string));
    }
}
