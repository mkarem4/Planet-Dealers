<?php

namespace App\Http\Controllers\Api;

use App\Events\BroadcastMsg;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,status,active,deleted,0,jwt,'.jwt(),
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $get_ids = Message::where('sender_id',$request->user_id)->orWhere('target_id',$request->user_id)->select('sender_id','target_id')->get();
        $ids = [];

        foreach($get_ids as $get_id)
        {
            if($get_id->sender_id != $request->user_id) $ids[] = $get_id->sender_id;
            else $ids[] = $get_id->target_id;
        }


        $users = User::whereIn('id',array_unique($ids))->select('id','first_name','last_name','image')->get();
        foreach($users as $user)
        {
            $message = User::getLatestMessageApi($request->user_id,$user->id);
            $user['message'] = $message;
//            $user['timestamp'] = strtotime($message->created_at);
        }

        $users = $users->sortByDesc('created_at')->values()->all();

        return response()->json($users);
    }


    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,status,active,deleted,0,jwt,'.jwt(),
                'with_id' => 'required|exists:users,id,status,active,deleted,0|not_in:'.jwt(),
            ]
        );


        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $messages = Message::where(function ($q) use($request)
        {
            $q->where('sender_id',$request->user_id)->where('target_id',$request->with_id);
            $q->orWhere('target_id',$request->user_id)->where('sender_id',$request->with_id);
        })->select('id','sender_id','text','created_at as timestamp')->read()->latest()->paginate(20);

        foreach($messages as $message) $message['text'] = 'm_p'.$message->text;

        return r_json($messages);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'sender_id' => 'required|exists:users,id,status,active,deleted,0,jwt,'.jwt(),
                'target_id' => 'required|exists:users,id,status,active,deleted,0|not_in:'.jwt(),
                'text' => 'required'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        $message = Message::create
        (
            [
                'sender_id' => $request->sender_id,
                'target_id' => $request->target_id,
                'text' => $request->text
           ]
        )->fresh();

        $payload = collect(['message' => $message,'date' => $message->created_at->toDateTimeString(),'image' => User::where('id',$message->sender_id)->select('image')->first()->image]);

        event(new BroadcastMsg($payload));

        $ar_text = 'لديك رسالة جديدة من '.$message->sender->name;
        $en_text = 'You got a new message from '.$message->sender->name;

        $notification = Notification::create
        (
            [
                'type' => 'message',
                'action_id' => $message->sender_id,
                'user_id' => $message->target_id,
                'ar_text' => $ar_text,
                'en_text' => $en_text,
            ]
        );

        $token = Token::where('user_id',$request->target_id)->orderBy('updated_at', 'desc')->pluck('token');

        $data['body'] = $notification->user->lang == 'ar' ? $ar_text : $en_text;
        $data['click_id'] = (integer)$message->sender_id;
        $data['click_action'] = 'chat';
        $data['data']['message'] = Message::where('id',$message->id)->select('sender_id','text','created_at as timestamp')->first()->toArray();
        $data['data']['user'] = User::where('id',$request->target_id)->select('id','first_name','last_name','image')->first()->toArray();
        $data['data']['other_user'] = User::where('id',$request->sender_id)->select('id','first_name','last_name','image')->first()->toArray();

        Notification::send($token,$data);

        return r_json([],204);
    }
}
