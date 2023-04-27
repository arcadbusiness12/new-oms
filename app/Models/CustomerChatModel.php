<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerChatModel extends Model
{
    public $timestamps = false;
    protected $table = 'customer_chats';
    protected $fillable = ['store','social','no_of_chat','date'];
}
