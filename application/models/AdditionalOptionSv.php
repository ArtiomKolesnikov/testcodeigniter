<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class AdditionalOptionSv extends Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'additional_option_sv';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public $timestamps = false;
}