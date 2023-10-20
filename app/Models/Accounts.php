<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of Accounts
 *
 * @author tonyl
 */
class Accounts extends Model {
    
    use SoftDeletes;
    protected $table = 'accounts';
    protected $properties = ['bank_id', 'owner', 'accountNumber', 'observations'];
    public function getProperties(){
        return $this->properties;
    }
    public function list(): BelongsTo{
        return $this->belongsTo('App\Bank');
    }
}
