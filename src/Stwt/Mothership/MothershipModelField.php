<?php namespace Stwt\Mothership;

use Log;

class MothershipModelField {

    public $name;
    public $null;
    public $default;
    public $key;
    public $extra;
    public $label;
    public $type;
    public $validation = [];
    public $tooltip;
    public $options;
    public $unsigned;

    protected $table;
    protected $dataType;

   /**
    * Create a new Field Specification from a sql row
    *
    * @param    object
    * @param    table
    * @param    array
    * @return   void
    */
    public function __construct($row=null, $table=null, $spec=[]) {

        if ( $row ) {

            foreach ($spec as $k => $v)
                $this->$k = $v;

            $this->table    = $table;

            $this->name     = $row->Field;
            $this->dataType = $row->Type;
            $this->null     = ($row->Null == 'YES' ? TRUE : FALSE);
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

        //Log::error($type);
        if (static::startsWith($type, 'int')) {
            $this->initInt();
        } else if (static::startsWith($type, 'varchar')) {
            $this->initVarchar();
        } else if (static::startsWith($type, 'float')) {
            $this->initFloat();
        } else if (static::startsWith($type, 'decimal')) {
            $this->initDecimal();
        } else if (static::startsWith($type, 'tinyint')) {
            $this->initTinyint();
        } else if (static::startsWith($type, 'datetime')) {
            $this->initDatetime();
        } else if (static::startsWith($type, 'date')) {
            $this->initDate();
        } else if (static::startsWith($type, 'timestamp')) {
            $this->initDatetime();
        } else if (static::startsWith($type, 'time')) {
            $this->initTime();
        } else if (static::startsWith($type, 'enum')) {
            $this->initEnum();
        } else if (static::startsWith($type, 'text')) {
            $this->initText();
        } else if (static::startsWith($type, 'blob')) {
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
    public function initField($spec=[])
    {
        foreach ($spec as $k => $v)
        {
            $this->$k = $v;
        }

        if( $this->key == 'PRI' )
        {
            $this->type = 'hidden';
            //$this->validation[] = 'unique:'.$this->table;
        }

        if( $this->key == 'UNI' )
        {
            // column must contain unique values
            $this->validation[] = 'unique:'.$this->table;
        }

        if( $this->null === TRUE )
        {
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
        if ($this->label)
            return;
        $this->label = static::humanize($this->name);
    }

   /**
    * Initialise a integer column
    *
    * - check if it's unsigned
    * - set the max and min values
    * - add any validation rules
    *
    * @access   private
    * @param    void
    * @return   void
    */
    private function initInt() 
    {
        $type = $this->dataType;
        // set default type
        if (!$this->type) $this->type = 'number';
        // check if unsigned
        if (static::endsWith($type, 'unsigned')) {
            $this->unsigned = TRUE;
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
        if (!$this->type) $this->type = 'number';

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
        if (!$this->type) $this->type = 'number';

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
        if (!$this->type) $this->type = 'checkbox_bool';
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
        if (!$this->type) $this->type = 'datetime';
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
        if (!$this->type) $this->type = 'date';
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
        if (!$this->type) $this->type = 'time';
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
        if (!$this->type) $this->type = 'text';

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
        if (!$this->type) $this->type = 'select';
        $options            = $this->getConstraint($type);
        $optionString       = str_replace('\'', '', $options);
        $this->validation[] = 'in:'.$optionString;
        $options            = explode(',', $optionString);
        $labels             = [];
        foreach ($options as $o)
            $labels[] = static::humanize($o);
        $options        = array_combine($labels, $options);
        $this->options = ($this->null ? array_merge(['-- None --' => NULL], $options) : $options);
    }

   /**
    * Initialise a text column
    *
    * @access   private
    * @param    void
    * @return   void
    */
    private function initText() {
        $type = $this->dataType; 
        // set default type
        if (!$this->type) $this->type = 'textarea';
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
    public function setForm() 
    {
        if ($this->type) return;

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
    * Sets some validation rules based on column type and length
    *
    * @access   public
    * @return   void
    */
    public function setValidation()
    {
        $dataType = $this->dataType;
        // extract any existing rules defined in the class
        // convert pipe separated rules into array
        if ($this->validation AND is_string($this->validation)) {
            $this->validation = explode('|', $this->validation);
        }

        // rules for primary keys
        if ($this->key == 'PRI') {
            $this->validation[] = 'required';
            $this->validation[] = 'exists:'.$this->table;
        }

        // check if unsigned
        if (static::endsWith($dataType, 'unsigned')) {
            $this->unsigned = TRUE;
            // remove from string
            $dataType = str_replace(' unsigned', '', $dataType);
        }

        // rules for scalar like columns (have length)
        $length = '';
        preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $dataType, $length);
        $length = current(end($length));
        if ($length) {
            $this->validation[] = 'max:'.$length;
            if ($this->isScalar()) {
                $this->max = $length;
                if ($this->unsigned)
                    $this->min = 0;
                else
                    $this->min = -$length;
            }
        }

        // date
        if ($dataType == 'date') {
            $this->validation[] = 'date_format:Y-m-d';
        }

        // timestamp
        if ($dataType == 'timestamp') {
            $this->validation[] = 'date_format:Y-m-d H:i:s';
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
    * Returns true if string ends with another string
    *
    * @access   protected
    * @param    string
    * @param    string
    * @return   boolean
    */
    protected static function endsWith($haystack, $needle)
    {
        return (substr($haystack, -strlen($needle)) === $needle);
    }

   /**
    * takes an underscored string and humanizes it
    *
    * @access   protected
    * @param    string
    * @return   string
    */
    protected function humanize($string) {
        return  ucwords(str_replace('_', ' ', $string));
    }
}