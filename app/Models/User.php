<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Laravel\Sanctum\HasApiTokens;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable  implements HasMedia {

  use HasApiTokens, HasFactory, SoftDeletes, InteractsWithMedia;

    const ADMIN = '0';
    const CUSTOMER = '1';
    const SPECIALIST = '2';
    const NORMAL_SIGNUP = 0; 
    const MEDIA_COLLECTION= 'user_profile';    
    const IS_ACTIVE= '1';
    const IS_INACTIVE= '0';  
    protected $table='users';
    protected $guarded = [];
    protected $primaryKey = "user_id";

    public function getFromDateAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }
    
    function getUserProfileImageAttribute() {
        $image = (!empty($this->profile_image) ) ? $this->getFirstMediaUrl(User::MEDIA_COLLECTION) : asset('admin_assets/images/default.png');
        return !empty($image)?$image:$this->profile_image;
    }

    public function isAdmin() {
        if ($this->user_type == User::ADMIN) {
            return true;
        } else {
            return false;
        }
    } 

}
