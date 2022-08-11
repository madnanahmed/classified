<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model  {

  protected $table = 'auctions';

  protected $fillable = [
      'user_id','ad_id','price','description','status', 'hour'
      ];

    protected $hidden = [];

  }
