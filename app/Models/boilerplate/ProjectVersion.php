<?php

namespace App\Models\boilerplate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Nicolaslopezj\Searchable\SearchableTrait;
use Cviebrock\EloquentSluggable\Sluggable;



class ProjectVersion extends Model
{
    use SearchableTrait,Sluggable;

    /**
     * Searchable rules.
     *
     * @var array
     */

    protected $table = 'projectversionings';

    public $fillable = ['version_number', 'description', 'application_type', 'version_code', 'created_by','slug'];


    public function sluggable():array
    {
        return [
            'slug' => [
                'source' => 'version_number'
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
            'version_number' => 10,
            'description' => 10,
            'application_type' => 5
        ],
    ];
}
