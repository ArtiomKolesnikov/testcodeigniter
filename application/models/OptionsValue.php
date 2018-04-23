<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class OptionsValue extends Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'options_value';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public $timestamps = false;
}