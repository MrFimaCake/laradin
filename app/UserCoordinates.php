<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCoordinates extends Model
{
	protected $table = 'user_coordinates';
    protected $fillable = [
        'user_id', 'coordinates',
    ];

    public function setCoordinatesAttribute($value)
    {
    	$this->attributes['coordinates'] = json_encode($value);
    }

    /**
     * @return array
     */
	public function getCoordinatesAttribute()
    {
        $coordinates = $this->attributes['coordinates'];
        
        return $coordinates ? json_decode($coordinates) : [];
    }


    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
