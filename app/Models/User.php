<?php
  
namespace App\Models;
  
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use QCod\ImageUp\HasImageUploads;

use App\Models\taxi\Driver;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\Target;
use App\Models\boilerplate\Country;
use App\Models\taxi\Requests\Request;
use App\Models\taxi\Vehicle;
use App\Models\taxi\RequestRating;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\UserComplaint;
use App\Models\taxi\DriverLogs;
use App\Models\taxi\DumpCompany;
use App\Models\boilerplate\CompanyDetails;
use App\Models\taxi\ReferalAmountList;
use App\Models\taxi\Referral;
use App\Traits\RandomHelper;

use Mail;
use App\Mail\TwoFactOtpMail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Sluggable,HasRoles,SoftDeletes,RandomHelper;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */



    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone_number',
        'password',
        'country',
        'avatar',
        'device_info_hash',
        'mobile_application_type',
        'login_method',
        'profile_pic',
        'referral_code',
        'token',
        'mobile_application_type',
        'online_by',
        'active',
        'block_reson',
        'language',
        'address',
        'emergency_number',
        'gender',
        'user_referral_code',
        'otp',
        'trips_count',
        'otp_expires_at',
        'others_user_id',
        'created_by'
    ];
  
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
  
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     public function getProfilePicAttribute($value)
     {
        if (empty($value)) {
            return null;
        }
          return getImage('images/profile',$value);
 }


    public function usertarget()
    {
        return $this->hasMany(Target::class, 'driver_id', 'id');
    }

    
   
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'firstname'
            ]
        ];
    }
    public function AauthAcessToken(){
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function getCountry()
    {
        return $this->hasOne(Country::class, 'id', 'country_code');
    }

    public function getDummyCompany()
    {
        return $this->hasOne(DumpCompany::class, 'user_id', 'id');
    }

    public function type()
    {
        return $this->hasOne(Vehicle::class,'id','type');
    }

    public function driver()
    {
        return $this->hasOne(Driver::class,'user_id','id');
    }
    

    public function rating()
    {
        return $this->hasMany(RequestRating::class,'user_id','id');
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class,'user_id','id');
    }

    public function wallettransaction()
    {
        return $this->hasMany(WalletTransaction::class,'user_id','id');
    }

    public function driverDocument()
    {
        return $this->hasMany(DriverDocument::class,'user_id','id');
    }
    
    public function driverRequestDetail()
    {
        return $this->hasMany(Request::class, 'driver_id', 'id');
    }
    
    public function UserRequestDetail()
    {
        return $this->hasMany(Request::class, 'user_id', 'id');
    }
    
    public function UserComplaintsList()
    {
        return $this->hasMany(UserComplaint::class, 'user_id', 'id');
    }
    
    public function DriverLogsList()
    {
        return $this->hasMany(DriverLogs::class, 'driver_id', 'id');
    }

    public function DriversLog()
    {
        return $this->hasOne(DriverLogs::class, 'driver_id', 'id')->latestOfMany();
    }

    public function companyDetails()
    {
        return $this->hasOne(CompanyDetails::class, 'user_id', 'id');
    }

    public function userGiveReferalAmount()
    {
        return $this->hasMany(ReferalAmountList::class, 'user_id', 'id');
    }

    public function userReceiveReferalAmount()
    {
        return $this->hasMany(ReferalAmountList::class, 'referal_user_id', 'id');
    }

    public function getOtherUser()
    {
        return $this->hasMany(Request::class, 'other_user_id', 'id');
    }

    public function generateCode()
    {

        if(auth()->user()->email == 'super@admin.com'){
            $code =1234;
            User::where('id',[auth()->user()->id])->update([
                 'otp' => $code ,
                'otp_expires_at' => now()->addMinutes(5)
        ]);
            try {
                    $details = $code;
                
            } catch (Exception $e) {
                info("Error: ". $e->getMessage());
            }
        }else{
            // $code = $this->UniqueRandomNumbers(4);
            $code =1234;
            User::where('id',[auth()->user()->id])->update([
                'otp' => $code ,
                'otp_expires_at' => now()->addMinutes(5)
                    ]);
            try {
                $details = $code;
            
           // Mail::to(auth()->user()->email)->send(new TwoFactOtpMail($details));
        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
        }
        
    }

}
