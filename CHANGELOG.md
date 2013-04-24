# Change Log

## 24th April 2013

* Fixed bug where foreign keys would be detected if "any" class existed with that name. Now we check that it's a class that extends from Mothership Model
* Changed setDefaultActions to getDefaultActions

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
