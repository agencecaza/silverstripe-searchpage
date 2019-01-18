<?php

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\FieldList;

class SearchPageExtension extends DataExtension {

  private static $db = [
    'ContentSearch' => 'Text'
  ];

  function updateCMSFields(FieldList $fields) {

    $fields->addFieldToTab(
      'Root.Main',
      HiddenField::create(
        'ContentSearch',
        'ContentSearch'
      )
    );
    
    return $fields;
  }
}
