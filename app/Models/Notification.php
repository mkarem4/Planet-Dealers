<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable =
        [
            'type','action_id','user_id','ar_text','en_text'
        ];


    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->select('id','lang');
    }


    public function getLTextAttribute()
    {
        $name = lang().'_text';
        return $this->$name;
    }


    public function getTimestampAttribute($value)
    {
        return strtotime($value);
    }


    public static function send($tokens,$data)
    {
//        $arr['content'] = $data;
        $arr = $data;
       
        $fields =
            [
                "registration_ids" => $tokens,
                "priority" => 10,
                'notification' => $arr,
                'data' => $arr,
                'vibrate' => 1,
                'sound' => 1
            ];

        $headers =
            [
                'accept: application/json',
                'Content-Type: application/json',
                'Authorization: key=' .
                'AAAAwWUua7A:APA91bFPi1RA1qsV7tWLe8bXq0mDOo4y0ayLn8Ani6qgF-VsTZvE32ds9hy6xxQlPMD0XSBzuSPoMEIuntCAX4EIPwLub_INj7frSk86yuHbYYjrxo7uaAmabT4EZQnyJ5SsQQ_MJn2E'
            ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE) die('Curl failed: ' . curl_error($ch));

        curl_close($ch);

        return $result;
    }
}
