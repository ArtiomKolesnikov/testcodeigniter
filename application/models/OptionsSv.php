<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class OptionsSv extends Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'options_sv';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public $timestamps = false;
}