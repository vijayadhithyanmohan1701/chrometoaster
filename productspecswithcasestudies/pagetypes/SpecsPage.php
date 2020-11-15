<?php

class SpecPage extends StandardInnerPage {

    protected static $singular_name = 'Spec';
    protected static $plural_name = 'Specs';

    private static $icon = 'Spec.png';

    protected static $db = [
        'Description'     => 'Varchar(255)',
        'ProductDescription' => 'Varchar(255)',
        'Reference'       => 'Varchar',
        'ReviewDate'      => 'Date',
        'Type'            => 'Enum("product, service","product")',
        'Order' 		  => 'Varchar(255)',
        'SpecsContent' => 'HTMLText',
        'SpecsImageTitle' => 'Varchar(255)',
        'CaseStudyTitle' 		  => 'Varchar(255)',
        'CaseStudyBlurb'	=> 'HTMLText',
        'References'	=> 'HTMLText'
    ];

    protected static $has_one = [
        'Category' => 'SpecCategory',
        'Download' => 'File',
        'DraftSpec' => 'File',
        'SecondaryDownload' => 'File',
        'PublishedSecondaryDownload' => 'File',
        'SpecsImage' => Image::class
    ];

    protected static $has_many = [
        'Products' => 'LicenseProduct',
        'Testimonials' => 'SpecsTestimonials'
    ];

    private static $default_parent = 'SpecHolderPage';

    private static $can_be_root = false;

    private static $summary_fields = [
        'Reference'      => 'Reference',
        'Description'    => 'Description',
        'Order' 		 => 'Order',
        'ReviewDate'     => 'Review Date',
        'Category.Title' => 'Category',
        'Products.Count' => 'Products',
        'Download.Name'  => 'Downloadable File',
        'Type'           => 'Type'
    ];

    private static $searchable_fields = [
        'Reference'   => 'Reference',
        'Description' => 'Description',
        'Category.ID' => ['title' => 'Category'],
        'Type'        => 'Type'
    ];

    public function getTitle() {
        return "({$this->Reference}) {$this->Description}";
    }

    public function getShortReference() {
        $exploded = explode('-', $this->Reference);
        if (isset($exploded[2])) {
            unset($exploded[2]);
        }

        return implode('-', $exploded);

    }

    public function getCMSFields() {
	    
	    $this->beforeUpdateCMSFields(function (FieldList $fields) {

            $TestimonialsConfig = new GridFieldConfig_RecordEditor();
            $TestimonialsConfig->addComponent(new GridFieldSortableRows('SortOrder'));
            $Testimonials = new GridField("TestimonialsGridField", "Testimonials", $this->Testimonials(), $TestimonialsConfig);
            $fields->addFieldToTab('Root.Testimonials', $Testimonials);
        });
				
        $fields = parent::getCMSFields();
        
        $categorySource = function () {
            return SpecCategory::get()->map()->toArray();
        };

        $fields->fieldByName('Root')->removeByName('Links');

        /** @var Tab $mainTab */
        $mainTab = $fields->fieldByName('Root.Main');

        if (!$this->exists()) {
            $fields->fieldByName('Root')->removeByName('Dependent');
        }

        $mainTab->setChildren(new FieldList([
            DropdownField::create('Type', 'Type', [
                'product' => 'Product',
                'service' => 'Service',
            ]),
            TextField::create('Reference', 'Reference'),
            TextField::create('Description', 'Description'),
            TextField::create('ProductDescription', 'Product Description'),
            DropdownField::create('CategoryID', 'Spec Category', $categorySource())->useAddNew('SpecCategory', $categorySource),
            TextField::create('CaseStudyTitle', 'Case study title'),
            HTMLEditorField::create('CaseStudyBlurb', 'Case Studies Info (Optional)'),
            TextField::create('Order', 'Order'),
            UploadField::create('Download', 'Published Spec')->setFolderName('Specs'),
            UploadField::create('PublishedSecondaryDownload', 'Secondary Published Spec')->setFolderName('Specs/Secondary'),
            
            DateField::create('ReviewDate', 'Review date')->setConfig('showcalendar', true),
            UploadField::create('DraftSpec', 'Draft Spec')->setFolderName('Specs/Draft'),
            UploadField::create('SecondaryDownload', 'Secondary Draft Spec')->setFolderName('Specs/Secondary'),
            TextField::create('SpecsImageTitle', 'Specs image title'),
            UploadField::create('SpecsImage', 'Additional Specs Image')->setFolderName('Uploads'),
            HTMLEditorField::create('SpecsContent', 'Specs content'),
            HTMLEditorField::create('References', 'Additional References')
        ]));
        return $fields;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $filter           = new URLSegmentFilter();
        $this->Title      = $this->getTitle();
        $this->MenuTitle  = $this->getTitle();
        $this->URLSegment = $filter->filter($this->Reference);
    }

    public function getSpecCompanies() {
        $companyIDs = (new SQLSelect('LicenseID'))
            ->setGroupBy('LicenseID')
            ->setFrom('LicenseProduct')
            ->setWhere("SpecID = {$this->ID}")
            ->execute()->column('LicenseID');

        return LicensePage::get()->filter('ID', $companyIDs);
    }

    public static function scopePublished() {
        return self::get()->where('ReviewDate IS NULL' || 'ReviewDate IS NOT NULL');
    }

    public static function scopeDraft() {
        return self::get()->where('ReviewDate IS NOT NULL');
    }

    public static function scopeHasProducts() {
        return self::get()->filterByCallback(function (SpecPage $page) {
            return $page->Products()->exists();
        });
    }
    public function getGateway() {
	    $gateway = DataObject::get_by_id('PublishedSpecsListPage', 27);
        return $gateway;
    }
    public function getCaseStudy() {
	    $caseStudy = DataObject::get('CaseStudy');
        return $caseStudy;
    }
    public function getTestimonials() {
	    $testimonials = DataObject::get('SpecsTestimonials', "SpecID = {$this->ID}");
        return $testimonials;
    }
	
	public function getLatestNews() {
	    $blogpost = DataObject::get("BlogPost"); 
		return $blogpost;
    }
    public function getLatestNewsByPageID(){
	    $retrievedposts = [];
	    $renderposts = new ArrayList();
	    $result = DB::query("SELECT StandardBlogPostID FROM StandardBlogPost_SpecsList WHERE SpecPageID = {$this->ID}");
	    if( $result ) {
		     foreach( $result as $item ) {
		          $blogpost = DB::query("SELECT * FROM BlogPost WHERE ID = {$item['StandardBlogPostID']}");
		         // Debug::show($blogpost);
		          foreach($blogpost as $post){
			          array_push($retrievedposts, $post);
		          }
		          
		     }
		}
		foreach($retrievedposts as $ret_post){
			
			$title = DB::query("SELECT Title, URLSegment, Content FROM SiteTree WHERE ID = {$ret_post['ID']}");
			
			foreach($title as $head){
				$renderposts->push(array(
						'CustomSummary' => $head['Content'],
						'CustomFeaturedImageID' => $ret_post['FeaturedImageID'],
						'CustomTitle' => $head['Title'],
						'CustomUrl' => $head['URLSegment'],
						
					));
			}
		}
		
		return $renderposts;
		
    }

}

class SpecPage_Controller extends StandardInnerPage_Controller {
	
}