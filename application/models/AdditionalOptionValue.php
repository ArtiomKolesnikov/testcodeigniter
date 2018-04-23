<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class AdditionalOptionValue extends Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'additional_option_value';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public $timestamps = false;
}