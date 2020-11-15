<?php
	
class SingleGalleryImage extends DataObject
{

    protected static $db = [
        'Title'     => 'Varchar(255)',
        'Displayed' => 'Boolean',
        'Year' => 'Date',
        'Category' => 'Varchar(255)',
        'SortOrder' => 'Int'
    ];

    protected static $has_one = [
        'Page'  => 'Page',
        'Image' => 'Image'
    ];

    private static $defaults = [
        'Displayed' => true
    ];

    protected static $default_sort = 'SortOrder';

    public function getCMSFields()
    {
        $fields = new FieldList([
            TextField::create('Title', 'Title/Caption'),
            DropdownField::create('Displayed', 'Displayed', [1 => 'Displayed', 0 => 'Hidden']),
            DateField::create('Year','Year')->setConfig('dateformat', 'yyyy'),
            TextField::create('Category', 'Category'),
            UploadField::create('Image', 'Image')->setFolderName("Images/MemberGalleryImages/".$this->Category)
        ]);
        
        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}