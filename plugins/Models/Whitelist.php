<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 16/09/2016
 * Time: 06:29
 */

namespace Plugin\Models;


use Illuminate\Database\Eloquent\Model;

class Whitelist extends Model
{
    protected $fillable = ['uid', 'added_by',];
}