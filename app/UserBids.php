<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserBids extends Model  {

  protected $table = 'user_bids';

  protected $fillable = [ 'auction_id', 'user_id', 'amount', 'paid', 'won' ];

    protected $hidden = [];

  }
