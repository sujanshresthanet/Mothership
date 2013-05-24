# Mothership

## The Resource Controller

The _MothershipResourceController_ provides basic the **CRUD** behaviour many of your admin Controllers will need. Each controller is mapped to a Model and will provide the following routes:

| Alias          | Method   | Route                  | Behaviour                                      |                           |
| -------------- | -------- | ---------------------- | ---------------------------------------------- | ------------------------- |
| **Collection** | GET      | controller/            | A collection view of resources e.g. a table    | [View][collectionSection] |
| **Create**     | GET      | controller/create      | A form to create a new resource                | [View][createSection]     |
| **View**       | GET      | controller/{id}        | A view page of a single resource instance      | [View][viewSection]       |
| **Edit**       | GET      | controller/{id}/edit   | A form to update an existing resource          | [View][editSection]       |
| **Delete**     | GET      | controller/{id}/delete | A form to delete an existing resource          | [View][deleteSection]     |
| **Store**      | PUT      | controller/            | Attempt to save a new instance of the resource | [View][storeSection]      |
| **Update**     | POST     | controller/{id}        | Attempt to update an existing resource         | [View][updateSection]     |
| **Destroy**    | DELETE   | controller/{id}        | Attempt to delement an existing resource       | [View][destroySection]    |

***

### Setup

To setup a new _MothershipResourceController_ follow the example below.

    <?php namespace MyApp\Admin;

    use Stwt\Mothership\MothershipResourceController as MothershipResourceController;

    class CarsController extends MothershipResourceController
    {
        public static $model = 'Car';
    }

To get the controller up and running you only need to specify one property, **$model**, this is the class name of the model you want _CarsController_ to manage.

Note: This property is static.

### Actions [actionsSection]

The action array defines the routes available to the controller. There are **four** types of actions in a resource controller.

#### Collection

These routes display collections of resources. Tables, lists, grids or search results. Generally they are paginated.

#### Single

These routes have no "state". They can be used to return create forms, help guides or info documents.

#### Resource

These routes are tied to a single record. Example the view, edit & delete routes. Generally they will have the resource id in the uri.

#### Related

These actions are routes to other related controllers.

***

### Collection Routes [collectionSection]

**GET**: _/admin/controller_

The list route returns a paginated table of resources.

***

### Create Routes [createSection]

**GET**: _/admin/controller/create_

The create route returns a form to create a new resource. The standard form will display all public database fields on the form. Forms are built using the [GoodForm][goodform] package. Out of the box, each column in the resources database is mapped to a form field. Documentation on customising your form fields can be found in the [Models][mothershipModels] section.

***

### View Routes [viewSection]

**GET**: _/admin/controller/{id}

The view route returns a resources details. The view is static and has no actions

***

### Edit Routes [editSection]

**GET**: _/admin/controller/{id}/edit_

The edit route returns a form to update an existing resource. The standard form will display all public database fields on the form. Forms are built using the [GoodForm][goodform] package. Out of the box, each column in the resources database is mapped to a form field. Documentation on customising your form fields can be found in the [Models][mothershipModels] section.

#### Extending

Sometimes complex models with lots of fields need more that one edit page. To add extra edit routes add a new [action][actionsSection] to the controller and extend the edit method in your controller.

For example, We have a car resource. The following method could be used to return an edit form that would just update interior fields like __Number of seats__, & __Has cup holders?__

    // ...CarsController

    public function interior ($id, $config = []) {
        $config = [
            // ...add customisation
        ];
        return $this->edit($id, $config);
    }

#### Parameters/Options

- $id     _int_   The resource id
- $config _array_ An array of config options

##### $id

This is the database id of the resource this route will create a form for. The _$id_ is piped into the method from the route.

##### $config \[optional\]

This associative array can be used to override some of the default behavious of the method. Generally you will only set it if you are extending the _edit_ method in your own controller.

The available options are:

###### $config\['title'\] (string)

The route **title** is displayed on both the page and html document title. Mothership uses Laravels [localization][laravelLocalization] features, page titles are defined in language files. 

By default the edit route will use the line at ``mothership.titles.edit``. If you have extended the edit method and are using another route, like the example above, then it will first look for a line at ``mothership.titles.interior``.

To set a custom language key you can add it to the config title attribute.

    // return string at the key 'my_custom_title' in lang/en/mothership.php
    $config['title'] = 'mothership.my_custom_title';

###### $config\['action'\] (string)

The **action** option lets you provide a differnt _url_ to submit the form to.

By default the edit routes action is controller/{id}. PUT requests to that url are routed to the controllers _update_ method. Custom actions like controller/{id}/interior will route PUT requests to the controllers _updateInterior_ method.

    // ..CarsController

    public function interior($id, $config = [])
    {
        $config['action'] = URL::to('admin/cars/'.$id.'/interior');
        return parent::edit($id, $config);
    }

    public function updateInterior($id, $config = [])
    {
        // ..handle form submission.
    }


###### $config\['fields'\] (array)

The **fields** option allows you to specifiy which of the resource fields will appear in the form. By default _all_ public fields are added to the form. Specify subset of field names to control the form.

To add custom fields, that are not tied to database properties, A _GoodForm_ compatible field array can be added to the array. A mixture of both strings and fields can be used together. In the example below, the first three fields are properties of the __Car__ model. The last item is a custom field that will be added to the form.

    //.. in CarsController

    public function interior($id, $config = []) {
        $config['fields'] = [
            'seat_material',
            'has_cup_holders',
            'number_of_seats',
            'package' => [
                'label' => 'Select an interior Package',
                'name'  => 'package',
                'type'  => 'select',
                'options' => [
                    'Custom'    => null,
                    'Standard'  => 1,
                    'Sport'     => 2,
                    'Executive' => 3,
                ],
            ],
        ];
        return parent::edit($id, $config);
    }

***

### Delete Routes [deleteSection]

**GET**: _/admin/controller/{id}/delete

The update route returns a form to confirm deletion of an existing resource.

***

### Store Route [storeSection]

**POST**: _/admin/controller

***

### Update Route [updateSection]

**PUT**: _/admin/controller/{id}

The update route handles forms submitted from the edit route. Out of the box it will attempt to update the resource instance with data from the **Input** class. Validation is run on the Input data before being assigned to the resource and saved.

#### Extending

You can extend the _update_ method in your model to customise how it handles a submitted form. Make sure you set the correct **action** in your _edit_ route so the PUT request is routed to the extended class.

The example below demonstraites how we can customise the PUT request from the interior route.

    // ...CarsController
    
    // handles forms with an action of admin/cars/{id}/interior
    public function updateInterior ($id, $config = []) {
        $config = [
            // ...add customisation
        ];
        return $this->update($id, $config);
    }

#### Parameters/Options

- $id     _int_   The resource id
- $config _array_ An array of config options

##### $id

This is the database id of the resource this route will attempt to update. The _$id_ is piped into the method from the route.

##### $config \[optional\]

This associative array can be used to override some of the default behavious of the method. Generally you will only set it if you are extending the _edit_ method in your own controller.

The available options are:

###### $config\['rules'\] (array)

An array of fields and their validation rules. Mothership uses the Laravel [Validation][laravelValidation] class

    $config['rules'] = [
        'seat_material'   => 'required',
        'number_of_seats' => ['required', 'min:1', 'numeric'],
    ];

###### $config\['beforeSave'\] (closure)

A closure called before resource is saved but after passing validation and assigned the form values. The closure takes one argument, an instance of the current resource. Objects in php are passed by reference, so there is no need to return anything. This is a great place to prep data before it's entered into the database. For example, Hashing passwords or tidying strings.

    $config['beforeSave'] = function ($resouce) {
        $resource->description = trim($resource->description);
    }

###### $config\['afterSave'\] (closure)

A closure called after the resource has been saved. The closure takes one argument, an instance of the current resource. Objects in php are passed by reference, so there is no need to return anything. This is a great place to handle post save events like sending out emails.

    $config['afterSave'] = function ($resouce) {
        Mail::send('welcome', $data, function($m) use ($resource)
        {
            $m->to($resource->email, $resouce->name)->subject('New account created!');
        });
    }

***

### Destory Route [destroySection]

**DELETE**: _/admin/controller/{id}

***

[mothershipModels]:     /models.md
[goodform]:             https://github.com/th3hamburgler/GoodForm  "GoodForm - a form building object"
[goodformDocs]:         https://github.com/th3hamburgler/GoodForm  "GoodForm - documentation"

[laravelLocalization]:  http://four.laravel.com/docs/localization
[laravelValidation]:    http://four.laravel.com/docs/validation
