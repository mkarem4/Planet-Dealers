<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class MerchantExport implements FromCollection,ShouldAutoSize
{
    public function collection()
    {
        $merchants =new Collection();

        $get_merchants = User::get();

        $collect['#'] = '#';
        $collect[word('type')] = word('type');
        $collect[word('status')] = word('status');
        $collect[word('country')] = word('country');
        $collect[word('name')] = word('name');
        $collect[word('company_name')] = word('company_name');
        $collect[word('email')] = word('email');
        $collect[word('phone')] = word('phone');
        $collect[word('pack')] = word('pack');
        $collect[word('featured')] = word('featured');
        $collect[word('created_at')] = word('created_at');

        $merchants = $merchants->push($collect);

        foreach($get_merchants as $merchant)
        {
            if($merchant->featured)
            {
                $featured = word('yes').' - '.word('till').' '.$merchant->featured_till;
            }
            else $featured = word('no');

            if($merchant->pack_id)
            {
                $pack = $merchant->pack->name.' - '.word('till').' '.$merchant->expire_at;
            }
            else $pack = word('no');

            $collect['#'] = $merchant->id;
            $collect[word('type')] = word($merchant->type);
            $collect[word('status')] = word($merchant->status);
            $collect[word('country')] = $merchant->country->name.' - '.$merchant->city->name;
            $collect[word('name')] = $merchant->name;
            $collect[word('company_name')] = $merchant->company_name;
            $collect[word('email')] = $merchant->email;
            $collect[word('phone')] = $merchant->phone;
            $collect[word('pack')] = $pack;
            $collect[word('featured')] = $featured;
            $collect[word('created_at')] = $merchant->created_at->toDateString();

            $merchants = $merchants->push($collect);
        }

        return $merchants;
    }
}
