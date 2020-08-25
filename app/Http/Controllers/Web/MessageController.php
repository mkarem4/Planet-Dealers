<?php

namespace App\Http\Controllers\Web;

use App\Events\BroadcastMsg;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function inbox()
    {
        $get_ids = Message::where('sender_id',user()->id)->orWhere('target_id',user()->id)->select('sender_id','target_id')->get();
        $ids = [];

        foreach($get_ids as $get_id)
        {
            if($get_id->sender_id != user()->id) $ids[] = $get_id->sender_id;
            else $ids[] = $get_id->target_id;
        }


        $users = User::whereIn('id',array_unique($ids))->select('id','first_name','last_name','image')->get();
        foreach($users as $user)
        {
            $message = User::getLatestMessage(user()->id,$user->id);

            $user['text'] = $message->text;
            $user['not_seen'] = Message::where(function ($q) use($user)
            {
                $q->where('sender_id',user()->id)->where('target_id',$user->id);
                $q->orWhere('target_id',user()->id)->where('sender_id',$user->id);
            })->where('seen',0)->count();
            $user['created_at'] = $message->created_at;

            $user->setAppends(['name']);
        }

        $users = $users->sortByDesc('created_at');
        $users->values()->all();

        return view('web.auth.inbox', compact('users'));
    }


    public function fetch(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'target_id' => 'required|exists:users,id,deleted,0',
            ]
        );

        if($validator->fails())
        {
            return response()->json(['status' => 'error', 'msg' => 'validation error', 'bag' => $validator->errors()]);
        }

        $messages = Message::where(function ($q) use($request)
        {
            $q->where('sender_id',user()->id)->where('target_id',$request->target_id);
            $q->orWhere('target_id',user()->id)->where('sender_id',$request->target_id);
        })->read()->select('sender_id','text','created_at as date_time')->get();

        foreach($messages as $message)
        {
            $message['sender'] = User::where('id',$message->sender_id)->select('id','first_name','last_name','image')->first();
        }

        $user = User::where('id',$request->target_id)->select('id','first_name','last_name','image')->first()->setAppends(['name']);
        $unread = user()->get_unread();

        return response()->json(['messages' => $messages,'user' => $user,'unread' => $unread]);
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'target_id' => 'required|exists:users,id,deleted,0|not_in:'.user()->id,
                'text' => 'required',
            ]
        );

        if($request->text == NULL) return response()->json(['status' => 'failed','msg' => 'empty message']);

        $message = Message::create
        (
            [
                'sender_id' => user()->id,
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
        $data['data']['message'] = Message::where('id',$message->id)->select('sender_id','text','created_at as timestamp')->first();
        $data['data']['user'] = User::where('id',$request->target_id)->select('id','first_name','last_name')->first();

        Notification::send($token,$data);

        return response()->json(['status' => 'success','message' => $message]);
    }


    public function store_profile(Request $request)
    {
        $this->validate($request,
            [
                'target_id' => 'required|exists:users,id,deleted,0|not_in:'.user()->id,
                'text' => 'required',
            ]
        );

        if($request->text == NULL) return response()->json(['status' => 'failed','msg' => 'empty message']);

        $message = Message::create
        (
            [
                'sender_id' => user()->id,
                'target_id' => $request->target_id,
                'text' => $request->text
            ]
        )->fresh();

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
        $data['data']['message'] = Message::where('id',$message->id)->select('sender_id','text','created_at as timestamp')->first();
        $data['data']['user'] = User::where('id',$request->target_id)->select('id','first_name','last_name')->first();

        Notification::send($token,$data);

        return redirect('/profile/inbox');
    }
}
