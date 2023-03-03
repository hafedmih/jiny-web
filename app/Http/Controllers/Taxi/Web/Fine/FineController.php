<?php

namespace App\Http\Controllers\Taxi\Web\Fine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\Web\FineRequest;

use App\Models\taxi\Fine;
use App\Models\User;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\Wallet;


class FineController extends Controller
{
    public function fine(Request $request)
    {
    	$fine = Fine::orderBy('created_at','desc')->get();

        return view('taxi.fine.index',['fine' => $fine]);
        
    }

    public function fineStore(FineRequest $request)
    {
        $data = $request->all();

        // $category = Category::where('slug',$data['category_id'])->first();

        // $filename =  uploadImage('images/vehicles',$request->file('image'));

        $fine = Fine::create([
            'driver_name' => $data['driver_name'],
            'fine_amount' => $data['fine_amount'],
            'user_id' => $request['user_id'],
            'description' => $data['description'],
            'date' => $data['date'],
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function fineUpdate(FineRequest $request)

    {
        $data = $request->all();


        $fine = Fine::where('slug',$data['driver_id'])->update([
            'driver_name' => $data['driver_name'],
            'fine_amount' => $data['fine_amount'],
            'description' => $data['description'],
            'date' => $data['date'],
        ]);
    }

    public function fineSavefunction(FineRequest $request)
    {
        $data = $request->all();

        $fine = Fine::create([
            'driver_name' => $data['driver_name'],
            'fine_amount' => $data['fine_amount'],
            'user_id' => $data['user_id'],
            'description' => $data['description'],
            'date' => $data['date'],
        ]);


        $user = User::where('slug',$data['user_id'])->first();
        $wallet = Wallet::where('user_id',$data['user_id'])->first();

        if($wallet){
            $wallet->amount_spent += $data['fine_amount'];
            $wallet->balance_amount -= $data['fine_amount'];
            $wallet->save();
        }
        else{
            $wallet = Wallet::create([
                'user_id' => $data['user_id'],
                'amount_spent' => $data['fine_amount'] ? $data['fine_amount'] : 0,
                'balance_amount' => $data['fine_amount'] ? 0 -$data['fine_amount'] :0,
                // 'amount_spent' => 0,
            ]);
        }

        $wallet_transaction = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' =>  $data['fine_amount'] ? 0 -$data['fine_amount'] : 0,
            'purpose' => 'Fine amount added successfully',
            'type' => 'SPENT',
            'user_id' => $data['user_id']
        ]);

        return response()->json(['message' =>'success'], 200);
    }

}