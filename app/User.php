<?php

namespace App;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use \DateTimeInterface;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasApiTokens, Billable;

    public $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'birth_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'credits',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');

    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();

    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;

    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;

    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }

    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));

    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);

    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function chargeCredits($hours, Room $room)
    {
        $amount = $hours * (int) $room->hourly_rate * 100;

        if ($this->credits < $amount) {
            return false;
        }

        $this->credits -= $amount;
        $this->save();

        Transaction::create([
            'user_id'      => $this->id,
            'room_id'      => $room->id,
            'paid_amount'  => $amount,
            'booking_time' => $hours,
        ]);

        return true;
    }

}
