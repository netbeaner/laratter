<?php

namespace App\Http\Controllers;

use App\Message;

use Illuminate\Http\Request;
use App\Http\Requests\CreateMessageRequest;

class MessagesController extends Controller
{
    //
    public function show(Message $message){

    	//$message = Message::find($id);

    	return view('messages.show', 
    		['message' => $message,

    	]);
    }

    public function create(CreateMessageRequest $request){

         $user = $request->user();
        $image = $request->file('image');

        $message = Message::create([
            'user_id' => $user->id,
            'content' => $request->input('message'),
            'image' => $image->store('messages', 'public')
        ]);

        return redirect('/messages/'.$message->id);
    	    	
    	
    }


    public function search(Request $request)
    {
        $query = $request->input('query');

       $messages = Message::with('user')->where('content', 'LIKE', "%$query%")->get();

        return view('messages.index', [
            'messages' => $messages,
        ]);
    }
}





