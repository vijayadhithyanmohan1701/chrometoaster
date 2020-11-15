<?php

class CommentsModelAdmin extends ModelAdmin
{

    private static $managed_models = [
        'CommentsSubmission'      => ['title' => 'Photo Gallery Comments']
    ];

    private static $url_segment = 'gallery-comments';

    private static $menu_title = 'Gallery Comments';
}