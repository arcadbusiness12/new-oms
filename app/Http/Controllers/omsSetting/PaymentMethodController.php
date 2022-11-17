<?php

namespace App\Http\Controllers\omsSetting;

use App\Http\Controllers\Controller;
use App\Models\Oms\PaymentMethodModel;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    const VIEW_DIR = 'oms_setting';

    public function __construct()
    {
        
    }

    public function paymentMethods() {
        $methods = PaymentMethodModel::all();
        return view(self::VIEW_DIR. '.paymentMethods')->with(compact('methods'));
    }

    public function addPaymentMethods(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required'
        ]);
        
        PaymentMethodModel::updateOrCreate(
            ['id' => $request->payment_method_id],
            [
                'name' => $request->name,
                'code' => $request->code,
                'fee'  => $request->fee,
                'status' => $request->status
            ]
            );

        return redirect()->back()->with('success', 'Payment Method added succussfully.');
    }
}
