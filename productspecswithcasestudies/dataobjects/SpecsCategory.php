<?php

class SpecsCategory extends DataObject {

    protected static $db = [
        'Title' => 'Varchar(255)',
        'Order' => 'Varchar(255)',
    ];

    protected static $has_many = [
        'Specs' => 'SpecsPage'
    ];

    protected static $default_sort = 'Title';

    private static $summary_fields = [
        'Title'       => 'Title',
        'Specs.Count' => 'Specs Count'
    ];

    public function getCMSFields() {
        $fields = new FieldList([
            TextField::create('Title'),
            TextField::create('Order'),
        ]);

        if($this->exists()){
            $fields->push(GridField::create("SpecsGridField", "Linked Specs", $this->Specs(), new GridFieldConfig_RecordViewer()));
        }

        return $fields;
    }

    public function getProductSpecs() {
        return $this->Specs()->filter('Type', 'product');
    }

    public function getServiceSpecs() {
        return $this->Specs()->filter('Type', 'service');
    }

    public function getHasProductSpecs() {
        return $this->getProductSpecs()->exists();
    }

    public function getHasServiceSpecs() {
        return $this->getServiceSpecs()->exists();
    }

    public function getHasSpecs() {
        return $this->Specs()->exists();
    }

    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

}