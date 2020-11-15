<?php

class TypeMembersPage extends Page
{

    private static $defaults = [
        'ShowInMenus' => false
    ];

    private static $db = [
        'Organization'                 => 'Varchar(255)',
        'Program'                      => 'Varchar(255)',
        'FacebookURL'                  => 'Varchar(255)',
        'TwitterURL'                   => 'Varchar(255)',
        'LinkedinURL'                  => 'Varchar(255)',
        'YoutubeURL'                   => 'Varchar(255)',
        'CurrentMember'                      => 'Boolean',
        'ContactEmailAddress'          => 'Varchar(255)',
        'WebsiteHomepageURL'           => 'Varchar(255)',
        'WebsiteStandardsURL'          => 'Varchar(255)',
        'Latitude'                     => 'Varchar',
        'Longitude'                    => 'Varchar',
        'PrimaryContactAddress'   => 'Varchar(255)',
        'PrimaryContactCountry'        => 'Varchar',
        'PrimaryContactPhone'          => 'Varchar',
        'PrimaryContactFax'            => 'Varchar',
        'SecondaryContactAddress' => 'Varchar(255)',
        'SecondaryContactCountry'      => 'Varchar',
        'SecondaryContactPhone'        => 'Varchar',
        'SecondaryContactFax'          => 'Varchar',

    ];

    private static $has_one = [
        'Logo' => 'Image'
    ];

    private static $many_many = [
        'ProductCategories' => 'ProductCategory'
    ];

    private static $default_parent = 'MemberListingPage';
    private static $can_be_root    = false;

    public function getLocation()
    {
        return $this->getTitle();
    }

    public function getCMSFields()
    {

        $this->beforeUpdateCMSFields(function (FieldList $fields) {

            $fields->renameField('Title', 'Location');
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Organization', 'Organization'),
                TextField::create('Program', 'Program'),
                ListboxField::create(
                    'ProductCategories', 'Product categories',
                    ProductCategory::get()->map()->toArray()
                )->setMultiple(true),
                DropdownField::create('CurrentMember', 'Current Member', [1 => 'Yes', 0 => 'No']),
                EmailField::create('ContactEmailAddress', 'Email address')
                          ->setAttribute('placeholder', 'email@domain.com'),
                TextField::create('WebsiteHomepageURL', 'Website homepage')
                         ->setAttribute('placeholder', 'http://...'),
                TextField::create('WebsiteStandardsURL', 'Standards page on website')
                         ->setAttribute('placeholder', 'http://...'),
                TextField::create('FacebookURL', 'Facebook URL')
                         ->setAttribute('placeholder', 'http://facebook.com/{username}'),
                TextField::create('TwitterURL', 'Twitter URL')
                         ->setAttribute('placeholder', 'http://twitter.com/{username}'),
                TextField::create('LinkedinURL', 'Linkedin URL')
                         ->setAttribute('placeholder', 'http://www.linkedin.com/in/{username}'),
                TextField::create('YoutubeURL', 'Youtube URL')
                         ->setAttribute('placeholder', 'http://www.youtube.com/user/{username}'),
                UploadField::create('Logo', 'Logo')->setFolderName('Logos'),
            ]);

            $fields->addFieldsToTab('Root.ContactAndAddress', [
                HeaderField::create('MapCoordinates', 'Map coordinates'),
                TextField::create('Latitude', 'Latitude'),
                TextField::create('Longitude', 'Longitude'),
                HeaderField::create('PrimaryContact', 'Primary contact'),
                TextField::create('PrimaryContactAddress', 'Primary Address'),
                TextField::create('PrimaryContactCountry', 'Country'),
                TextField::create('PrimaryContactPhone', 'Phone'),
                TextField::create('PrimaryContactFax', 'Fax'),
                HeaderField::create('SecondaryContact', 'Secondary contact'),
                TextField::create('SecondaryContactAddress', 'Secondary Address'),
                TextField::create('SecondaryContactCountry', 'Country'),
                TextField::create('SecondaryContactPhone', 'Phone'),
                TextField::create('SecondaryContactFax', 'Fax'),
            ]);

        });

        $fields = parent::getCMSFields();
        $fields->removeByName('SubTitle');
        $fields->removeByName('MenuTitle');
        $fields->removeByName('URLSegment');
        $fields->removeByName('Content');

        return $fields;
    }

    public function getMapContent()
    {
        /** @var HTMLText $object */
        $object  = HTMLText::create();
        $content = '<h4>' . $this->Title . '</h4><h5>' . $this->Organization . '</h5><p><a href="' . $this->Link() . '">View More</a></p>';
        $object->setValue($content);
        return $object;
    }

    protected function onBeforeWrite()
    {
        $this->URLSegment = null;
        parent::onBeforeWrite();
    }


}

class TypeMembersPage_Controller extends Page_Controller
{
}
