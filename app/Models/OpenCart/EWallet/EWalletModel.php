<?php 

namespace App\Models\OpenCart\EWallet;

use App\Models\OpenCart\AbstractOpenCartModel;

class EWalletModel extends AbstractOpenCartModel {
    public $timestamps = false;
    protected $table = 'e_wallet_transaction';

    const FIELD_CUSTOMER_ID = 'customer_id';
    const FIELD_PRICE = 'price';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_DATE_ADDED = 'date_added';
    const FIELD_BALANCE ='balance';
}