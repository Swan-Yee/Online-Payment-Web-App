<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        $user = auth()->guard('web')->user();
        $notifications = $user->notifications()->orderBy('read_at','asc')->paginate(5);

        return view('frontend.notification',compact('notifications'));
    }

    public function show($id){
        $user = auth()->guard('web')->user();
        $notification = $user->notifications()->where('id',$id)->firstOrFail();

        $notification->markAsRead();

        return view('frontend.notification-detail',compact('notification'));
    }

    public function readAll(){
        $user = auth()->guard('web')->user();
        $user->unreadNotifications->markAsRead();
        return redirect()->back();
    }
}
