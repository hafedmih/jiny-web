<?php

namespace App\Models\boilerplate\RolePermission;

// use Zizaco\Entrust\EntrustPermission;

use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Permission extends Model
{

    use SearchableTrait,Sluggable;

    /**
     * Searchable rules.
     *
     * @var array
     */
    public function sluggable():array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'name' => 10,
            'display_name' => 10,
            'category' => 5
        ],
    ];
    
}
