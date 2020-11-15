<?php

class CommentsSubmission extends DataObject
{

    protected static $db = [
        'ImageId'    => 'Varchar(15)',
        'Comments'   => 'Varchar(255)',
        'PostedBy'	=> 'Varchar(255)',
        'EnableComments'	=>	'boolean'
    ];
    protected static $summary_fields = [
        'ImageId' => 'Image ID',
        'Comments' => 'Comment Posted',
        'Created'	=> 'Date Posted',
        'PostedBy'	=>	'Posted By',
        'EnableComments'	=> 'Enabled'
    ];

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