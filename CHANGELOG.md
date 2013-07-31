# Change Log

## 31st July

* Added deleted_at to datetime fields that get auto hidden in forms
* Added an observer on the created event that will set a resources default 'order' value to it's id

## 18th July

* Added beforeDestoy callback to destoy method

## 17th July

* Added default field array to the base model
* Added editHasOne and updateHasOne methods

## 10th July

* Added iconClass and nicename methods to the FileModel

## 3rd July

* Switched order getProperties detectes properties and closures to fix bug where some properties were flagged as closures
* Added new humanize string helper

## 1st July

* Added appTitle config item
* Added appStyle config item
* Added appScript config item
* Allow homepage overrides
* Added selectable config for table view
* Fixed bug allowing multiple max:size rules to be added

## 15th June

* Added initProperties so closures can be added to $properties without the need to override the constructor

## 3rd June

* Added Image picker to forms

## 2nd June

* Added mass deletion to collection views
* Added FileModel and FileController
* Added ImageModel and ImageController

## 1st June

* Added a new FormGenerator class to move form building logic outside of the controllers
* Moved controllers to the controller dir and renamed, removing the Mothership prefix
* Added HomeController class
* Moved to use the Eloquent guarded array instead of hidden for generating default forms.
* Fixed bug where getRules would return guarded field rules by defualt

## 31st May 2013

* Date fields now return a "relative" date string in tables by default. e.g. "7 hours ago"
* Fixed bug with auto creating select element for foreign keys. Was setting $type to select, not $form.
* Updated the singular and plural functions
* Added 'Update Profile' & 'Change Password' actions to the home controller

## 30th May 2013

* Added model property caching
* Added a proper config file template
* Made the home controller dynamic
# Added related collection views
# Added related breadcrumbs

## 24th May 2013

* Bug with MothershipModelField. Failed to detect subclasses of MothershipModel. Included namespace to class name to fix
* Added bootstrap datetimepicker support
* Removed colorpicker plugin for now as it causes conflict with datetimepicker

## 14th May 2013

* Bug in the getFields method would return 0 rules if no parameter array was set

## 9th May 2013

* Added redirect on success field to update method (update this with more elegant solution later!)

## 24th April 2013

* Fixed bug where foreign keys would be detected if "any" class existed with that name. Now we check that it's a class that extends from Mothership Model
* Removed setDefaultActions, actions are now defined in the $actions class property. Added the {controller} placeholder so actions can be used in extended controllers
* Edit forms now include a _redirect field so we can return to the correct route after updating.
* Added dataCallback. This returns Input data from a form to be saved to the resource. This is accessed **after** validation. Use Case: hash a password after it has been validated.

## 22nd April 2013

* Fixed bug in code that detects foreign keys. Now makes sure the class name is in StudlyCase so the class name will be found on case sensitive systems.
* Updated the 'update' method. It will now validate fields posted to the form that have changed value OR that are empty. This picks up any required fields that were not entered.

## 20th April 2013

* Removed the $canBeNull property as no longer needed. We now check if a column has a default value set. If it has, there's no need to set it as required automatically.

## 19th April 2013

* Fixed logic for guessing if a field is required. Mothership will now set fields as required if the column does not allow NULL values, is not the primary key or a '$canBeNull' field like created_at and updated_at.

## 17th April 2013

* Added getTable() to the MothershipModelField class. This is used to return related models as a string at the moment. In future it can be used to return other fields formatted for tables

## 16th April 2013

* Automatically create select field out of foreign key fields if the model exists in the field name. e.g. modelname_id
* Changed the default toString to return _Model Name: id_

## 15th April 2013

* Added Revisionable package to the mothership: https://github.com/VentureCraft/revisionable. All models are now Revisionable by default. Resource change history can be viewed in the CMS. _make sure to run_ 
    php artisan migrate --package=venturecraft/revisionable
