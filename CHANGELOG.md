# Change Log

## 16th April 2015

* Automatically create select field out of foreign key fields if the model exists in the field name. e.g. modelname_id
* Changed the default toString to return _Model Name: id_

## 15th April 2013

* Added Revisionable package to the mothership: https://github.com/VentureCraft/revisionable. All models are now Revisionable by default. Resource change history can be viewed in the CMS. _make sure to run_ 
    php artisan migrate --package=venturecraft/revisionable
