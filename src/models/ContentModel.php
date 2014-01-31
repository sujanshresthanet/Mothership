<?php namespace Stwt\Mothership;

use ContentItem;

/**
 * An example of how to write code to PEAR's standards
 *
 * @category   
 * @package    
 * @copyright  
 * @license    
 * @version    
 * @link       https://github.com/th3hamburgler/Mothership
 */
class ContentModel extends BaseModel
{
    /**
     * Name of the mysql table for this model
     * 
     * @var string
     */
    protected $table = "contents";

    protected $singular = "content block";

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
    protected $properties = [
        'html' => [
            'class' => 'html',
        ],
    ];

    /**
     * Default fields that will appear in the admin form
     * @var array
     */
    protected $fields = [
        'slug',
        'html',
        'type',
    ];


    /**
     * Initialise custom table columns
     * 
     * @param  array $columns The models existing columns array
     * @return array
     */
    protected function initColumns($columns)
    {
        $columns = [
            'slug',
            'type',
            'page_id',
        ];

        return $columns;
    }

    // ------------------------- //
    // Eloquent Relationships    //
    // ------------------------- //
    
    public function contentItems()
    {
        App::abort(501, 'Please define ContentItem relationship in you Content Model');
        
        return $this->morphMany('ContentItem', 'content');
    }

    /**
     * --------------------------------------
     */
    
    public function scopeShared($query)
    {
        return $query->where('type', '=', 'shared');
    }

    /**
     * --------------------------------------
     */
    
    public function generate()
    {
        return $this->content;
    }

    public function type()
    {
        switch ($this->type) {
            case 'html':
                return 'HTML';
                break;
            case 'text':
                return 'Text';
                break;
            case 'string':
                return 'String';
                break;
            default:
                return ucfirst($this->type);
                break;
        }
    }
}
