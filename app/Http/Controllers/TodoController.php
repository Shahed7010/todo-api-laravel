<?php

namespace App\Http\Controllers;

use App\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Todo::where('user_id',auth()->user()->id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
           'title' => 'required|string'
        ]);
        $todo = Todo::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'completed' => $request->completed,
        ]);
        return response($todo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'completed' => 'required|boolean'
        ]);
        $todo->update($data);
        return response($todo, 200);

    }
    public function markCompleted(Request $request)
    {
        $data = $request->validate([
            'completed' => 'required|boolean'
        ]);
//        Todo::query()->update($data);
        Todo::where('user_id', auth()->user()->id)->update($data);
        return response('updated', 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        if ($todo->delete()){
         return response('deleted successfully', 200);
        }

    }
    public function destroyCompleted($request)
    {
//        $request->validate([
//            'todos' => 'required|array'
//        ]);
        $user_todos = auth()->user()->todos->map(function ($todo){
            return $todo->id;
        });
        $todos = explode(',',$request);
        $user_todos_array = json_decode(json_encode($user_todos), true);

        $check = array_diff($todos, $user_todos_array);

        if(!empty($check)){
            return response()->json('unauthorized to delete', 401);
        }
        Todo::destroy($todos);
         return response($todos, 200);

    }
}
