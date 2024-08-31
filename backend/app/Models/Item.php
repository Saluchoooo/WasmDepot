<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class Item extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'items';

    protected $fillable = [
        'title',
        'description',
        'installation',
        'image_urls',
        'file_url',
        'github_url',
        'web_url',
        'owner',
        'last_update',
        'categories',
        'type',
        'creator'
        
    ];
}
