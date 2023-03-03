<?php

namespace App\Models\boilerplate\RolePermission;

// use Zizaco\Entrust\EntrustRole;

use Nicolaslopezj\Searchable\SearchableTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	use SearchableTrait;
    use Sluggable;

	protected $fillable = ['name', 'display_name'];

   
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
            'display_name' => 5
        ],
    ];	

}
