<?php

class TypeMember extends DataObject
{

    protected static $db = [
	    'Title'               => 'Varchar(255)',
	    'FullName'					 => 'Varchar(255)',
        'WebsiteHomepageURL'         => 'Varchar(255)',
        'Latitude'                   => 'Varchar',
        'Longitude'                  => 'Varchar',
        'PrimaryContactAddress' => 'Varchar(255)',
        'PrimaryContactCountry'      => 'Varchar',
        'PrimaryContactPhone'        => 'Varchar',
        'PrimaryContactFax'          => 'Varchar',
        'ContactEmailAddress'        => 'Varchar(255)',

        'SortOrder' => 'Int'
    ];

    protected static $has_one = [
        'Logo' => 'Image',
        'Page' => 'TypeMemberListingPage'
    ];

    protected static $default_sort = 'SortOrder';

    public function getCMSFields()
    {

        $fields = new FieldList([
            TextField::create('Title', 'Organization'),
            TextField::create('FullName', 'Full Name'),
            EmailField::create('ContactEmailAddress', 'Email address')
                      ->setAttribute('placeholder', 'email@domain.com'),
            TextField::create('WebsiteHomepageURL', 'Website homepage')
                     ->setAttribute('placeholder', 'http://...'),
            UploadField::create('Logo', 'Logo')->setFolderName('Logos'),
            HeaderField::create('MapCoordinates', 'Map coordinates'),
                TextField::create('Latitude', 'Latitude'),
                TextField::create('Longitude', 'Longitude'),
            HeaderField::create('PrimaryContact', 'Primary contact'),
            TextField::create('PrimaryContactAddress', 'Address'),
            TextField::create('PrimaryContactCountry', 'Country'),
            TextField::create('PrimaryContactPhone', 'Phone'),
            TextField::create('PrimaryContactFax', 'Fax'),
        ]);

        return $fields;
    }

    public function getOrganization()
    {
        return $this->getTitle();
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
    public function getMapContent()
    {
        /** @var HTMLText $object */
        $object  = HTMLText::create();
        $content = '<h4>' . $this->PrimaryContactCountry . '</h4><h5>' . $this->Title . '</h5><p><a href="/members/type-members/">View More</a></p>';
        $object->setValue($content);
        return $object;
    }

}