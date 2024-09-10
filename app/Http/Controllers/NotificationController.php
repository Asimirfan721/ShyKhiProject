<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationController extends Controller
{
    public function index(){
        return view('backend.notification.index');
    }
    public function show(Request $request){
        $user = Auth::user();
    
        if(!$user){
            session()->flash('error', 'No authenticated user found.');
            return back();
        } 
    
        $notification = $user->notifications()->where('id', $request->id)->first();
        
        if($notification){
            $notification->markAsRead();
            return redirect($notification->data['actionURL']);
        } else {
            session()->flash('error', 'Notification not found.');
            return back();
        }
    }public function delete($id){
        $notification=Notification::find($id);
        if($notification){
            $status=$notification->delete();
            if($status){
               session()->flash('success','Notification successfully deleted');
                return back();
            }
            else{
               session()->flash('error','Error please try again');
                return back();
            }
        }
        else{
            session()->flash('error','Notification not found');
            return back();
        }
    }
}
