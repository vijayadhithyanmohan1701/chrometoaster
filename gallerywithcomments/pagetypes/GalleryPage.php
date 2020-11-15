<?php

class MembersGalleryPage extends Page
{

    protected static $singular_name = 'Members Gallery Page';
    protected static $plural_name = 'Members Gallery Pages';
    
	protected static $db = [
        'FolderName'     => 'Varchar(255)'
    ];
    protected static $has_many = [
        'GalleryImages' => 'SingleGalleryImage'
    ];

    public function getCMSFields()
    {
	    //$url_path = "Images/MemberGalleryImages/";

        $this->beforeUpdateCMSFields(function (FieldList $fields) {

            $fields->removeByName('MembersGalleryImages');
			$fields->addFieldToTab("Root.MembersGallery", TextField::create('FolderName', 'Folder Name'));
            $GridFieldConfig = new GridFieldConfig_RecordEditor();
            $GridFieldConfig->removeComponentsByType('GridFieldAddNewButton');
            $GridFieldConfig->addComponent($bulkUploadConfig = new GridFieldBulkUpload());
            $GridFieldConfig->addComponent(new GridFieldSortableRows('SortOrder'));
            $GridFieldConfig->addComponent(new GridFieldGalleryTheme('Image'));
            $bulkUploadConfig->setUfSetup('setFolderName', "Images/GalleryImages/");
            $GridField = new GridField("MembersGalleryImages", 'Members Gallery', $this->MembersGalleryImages(), $GridFieldConfig);

            $fields->addFieldToTab("Root.MembersGallery", $GridField);
        });

        $fields = parent::getCMSFields();

        return $fields;
    }
    
	
}

class MembersGalleryPage_Controller extends Page_Controller
{
	private static $allowed_actions = array(
        'ImageSearchForm',
        'ImageCommentsForm'
    );

	public function ImageSearchForm()
    {
	    Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.min.js');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-validate/jquery.validate.min.js');
        Requirements::javascript('themes/default/src/javascript/dependentdropdownfield.js');
	    $folder_parent = DataObject::get_one("Folder", "Filename = 'assets/Images/MemberGalleryImages/'");
	    $folders_level1 = DataObject::get("Folder", "ParentID = '{$folder_parent->ID}'");
	    $folders_lev1 = [];
	    $folders_lev2 = [];
	    $folders_lev1[''] = '---';
		$folders_level2 = [];
	    	foreach($folders_level1 as $level1){
		    	$folders_lev1[$level1->Name] = $level1->Title;
		    	$folders_level2 = DataObject::get("Folder", "ParentID = '{$level1->ID}'");
		    		foreach($folders_level2 as $level2){
			    		$folders_lev2[$level2->ID] = $level2->Title;	
			    	}
	    	}
	    	
	    	if(isset($_GET['Year']) && $_GET['Year'] != '' ){
		    	unset($folders_lev2);
		    	$folder_parent_level2 = DataObject::get_one("Folder", "Filename = 'assets/Images/MemberGalleryImages/{$_GET['Year']}/'");
				$folders_level2_test = DataObject::get("Folder", "ParentID = '{$folder_parent_level2->ID}'");
				foreach($folders_level2_test as $level2_test){
			    		//Debug::show($level2_test);
			    		
			    		$folders_lev2[$level2_test->Name] = $level2_test->Title;
			    	}
	    	}
	   //Debug::show($folders_level2);
        $form = Form::create(
            $this,
            'ImageSearchForm',
            FieldList::create(
                DropdownField::create('Year','Choose Year')                   
                    ->setSource($folders_lev1)
                    ->addExtraClass('form-control'),
                DropdownField::create('EventType','Choose Event')                   
                    ->setSource($folders_lev2 )
                    ->addExtraClass('form-control')
                    ->setEmptyString('Select event')
            ),
            
            FieldList::create(
                FormAction::create('doImageSearch','Search')
                    ->addExtraClass('btn-lg btn-fullcolor'),
                   ResetFormAction::create('doReset','Reset')
                    ->addExtraClass('btn-lg btn-fullcolor')
            )
        );
		$form->setFormMethod('GET')
         ->setFormAction($this->Link())
         ->disableSecurityToken();
        return $form;
    }
    public function ImageCommentsForm() {
		$fields = FieldList::create(
			HiddenField::create('ImageId', 'Image ID'),
			TextareaField::create('Comments', 'Your Comments')->setAttribute('required', true)
		);
		$actions = FieldList::create(
			FormAction::create("doPostComment")->setTitle("Post Comment")->addExtraClass('btn-lg btn-fullcolor')
		);
		$form = Form::create($this, 'ImageCommentsForm', $fields, $actions);
		
		return $form;
	}
    public function FrontPageImages() {
	  $images = '';
	  if((!isset($_GET['Year']) || $_GET['Year'] == '' || $_GET['Year'] == '0') && (!isset($_GET['EventType']) || $_GET['EventType'] == '' || $_GET['EventType'] == '0')){
		  $images = DataObject::get(
			$name = 'Image',
			$filter = "ClassName = 'image' and Filename like 'assets/Images/MemberGalleryImages/%'",
			$sort = "Created DESC",
			$join = "",
			$limit = ""
		);
	  }else if((isset($_GET['Year']) || $_GET['Year'] != '' || $_GET['Year'] != '0') && (!isset($_GET['EventType']) || $_GET['EventType'] == '' || $_GET['EventType'] == '0')){
		  $images = DataObject::get(
			$name = 'Image',
			$filter = "ClassName = 'image' and Filename like 'assets/Images/MemberGalleryImages/%{$_GET['Year']}%'",
			$sort = "Created DESC",
			$join = "",
			$limit = ""
		);
	  }else{
		  $images = DataObject::get(
			$name = 'Image',
			$filter = "ClassName = 'image' and Filename like 'assets/Images/MemberGalleryImages/%{$_GET['Year']}%/%{$_GET['EventType']}%'",
			$sort = "Created DESC",
			$join = "",
			$limit = ""
		);
	  }
	  
		foreach($images as $img){
			$img->ImageComments = $this->ImageComments($img->ID);
		}
		return PaginatedList::create($images, $this->getRequest())->setPageLength(12);
	}
	public function ImageComments($id){
		$comments = DataObject::get(
			$name = 'ImageCommentsSubmission',
			$filter = "ImageId = $id",
			$sort = "Created DESC",
			$join = "",
			$limit = ""
		);
		return $comments;
	}
    public function doSubmit($data, $form)
    {
        if ($this->request->isAjax()) {
            return $this->customise(array(
                'year' => $_GET['Year']
            ))->renderWith('themes/default/src/javascript/dependentdropdownfield.js');
        } else {
            //This is a failsafe
        }
    }
    public function doPostComment($data, $form, $request) {
	    $data = $form->getData();
	    $member = Member::currentUser();
		$comments = new ImageCommentsSubmission();
		$comments->ImageId = $data['ImageId'];
		$comments->Comments = $data['Comments'];
		$comments->PostedBy = $member->FirstName.' '.$member->Surname;
		$page_num = '';
		if(isset($_REQUEST['start'])){
			$page_num = "&start=".$_REQUEST['start'];
		}else{
			$page_num = '';
		}
		$data['page_num'] = $page_num;
		$comments->write();
		Session::set('ActionMessage', '<p class="session-success">Your comment has been posted successfully. <a  data-id="'.$data['ImageId'].'" class="test comment-post-success">Click here</a> to view your comment.</p>'.$data['page_num']);
		Session::set('CommentsPosted', true);
		return $this->redirect($this->AbsoluteLink() . "/?comment_success=yes".$page_num);
        
	}
	public function StatusMessage()
    {
        $message = Session::get('ActionMessage');
			$status = Session::get('CommentsPosted');
        if(isset($_REQUEST['comment_success']) && $_REQUEST['comment_success'] == "yes"){
	        return new ArrayData(array('Message' => $message, 'Status' => $status));
        }else{
	        return false;
        }
    }
    
}
