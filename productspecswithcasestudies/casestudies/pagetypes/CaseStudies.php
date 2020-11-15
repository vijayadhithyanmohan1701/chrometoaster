<?php

class CaseStudiesPage extends Blog {

    private static $allowed_children = ['CaseStudy'];

    public function getLumberjackTitle() {
        return 'Case Studies';
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('Tags');

        return $fields;
    }


}

class CaseStudiesPage_Controller extends Blog_Controller {
}
