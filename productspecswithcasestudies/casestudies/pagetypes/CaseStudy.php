<?php

class CaseStudy extends BlogPost {
    private static $default_parent = 'CaseStudiesPage';

    private static $singular_name = 'Case Study';

    private static $db = [
        'Initiative'          => 'Text',
        'LicenseName'        => 'Varchar(255)',
        'Website'             => 'Varchar(255)',
        'SectionOneTitle'     => 'Varchar(255)',
        'SectionTwoTitle'     => 'Varchar(255)',
        'SectionThreeTitle'   => 'Varchar(255)',
        'SectionOneContent'   => 'HTMLText',
        'SectionTwoContent'   => 'HTMLText',
        'SectionThreeContent' => 'HTMLText',
    ];

    private static $many_many = [
        'Specs' => 'SpecPage'
    ];

    private static $has_one = [
        'SectionOneImage'   => 'Image',
        'SectionTwoImage'   => 'Image',
        'SectionThreeImage' => 'Image',
        'TheCompanyImage'   => 'Image',
    ];

    private static $defaults = [
        'SectionOneTitle'   => 'The Challenge',
        'SectionTwoTitle'   => 'The Solution',
        'SectionThreeTitle' => 'The Benefits',
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $rootTab = $fields->fieldByName('Root');

        $rootTab->insertAfter('Main', Tab::create('TheCompany'));
        $rootTab->insertAfter('TheCompany', Tab::create('SectionOne')->setTitle($this->SectionOneTitle ?: 'Section One'));
        $rootTab->insertAfter('SectionOne', Tab::create('SectionTwo')->setTitle($this->SectionTwoTitle ?: 'Section Two'));
        $rootTab->insertAfter('SectionTwo', Tab::create('SectionThree')->setTitle($this->SectionThreeTitle ?: 'Section Three'));

        $fields->addFieldsToTab('Root.Main', [
            TextareaField::create('Initiative', 'Initiative'),
            TagField::create('Specs', 'Specs', SpecPage::get(), $this->Specs()),
            TextField::create('Website', 'Website')->setAttribute('placeholder', 'http://...')
        ], 'FeaturedImage');

        foreach (['SectionOne', 'SectionTwo', 'SectionThree'] as $section) {
            $fields->addFieldsToTab("Root.{$section}", [
                TextField::create("{$section}Title", 'Section Title'),
                HtmlEditorField::create("{$section}Content", 'Content'),
                UploadField::create("{$section}Image", 'Image')->setFolderName('Images')
            ]);
        }

        $fields->addFieldsToTab('Root.TheCompany', [
            TextField::create('SubTitle', 'Company Name'),
            UploadField::create('TheCompanyImage', 'Image')->setFolderName('Images'),
            HTMLEditorField::create('Content', 'About The Company')
        ]);

        $fields->removeByName('Tags');
        $fields->removeByName('CustomSummary');

        $fields->fieldByName('Root.Main.FeaturedImage')->setFolderName('Images');

        return $fields;
    }

    public function getTheCompanyName() {
        return $this->SubTitle;
    }

    public function getTheCompanyContent() {
        return $this->Content;
    }

    public function fieldLabels($includeRelations = true) {
        $labels = parent::fieldLabels($includeRelations);

        $labels['Title'] = 'Case Study Title';

        return $labels;
    }

    public function getWebsiteNice() {
        return str_replace('http://', '', $this->Website);
    }
}

class CaseStudy_Controller extends BlogPost_Controller {
}
