<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ログイン中のユーザーidを取得
        $user_id = Auth::guard('api')->user()->id;
        // ログインユーザーのタスクのみ取得
        $tasks = Task::where('user_id', $user_id)->get();
        return response()->json([
            'data' => $tasks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ログイン中のユーザーidを取得
        $user_id = Auth::guard('api')->id();
        // ログインユーザーのタスク追加
        $task = Task::create(
            [
                'user_id' => $user_id,
                'title' => $request->input('title'),
                'is_done' => $request->input('is_done')
            ]
        );
        // ログインユーザーのタスクのみ取得
        $tasks = Task::where('user_id', $user_id)->get();
        return response()->json([
            'data' => $tasks
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        // ログイン中のユーザーidを取得
        $user_id = Auth::guard('api')->id();
        // タスクの更新
        $update = [
            'title' => $request->input('title'),
            'is_done' => $request->input('is_done')
        ];
        $item = Task::where('id', $task->id)->update($update);
        // ログインユーザーのタスクのみ取得
        $tasks = Task::where('user_id', $user_id)->get();
        if ($item) {
            return response()->json([
                'data' => $tasks
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        // ログイン中のユーザーidを取得
        $user_id = Auth::guard('api')->id();
        // タスクの削除
        $item = Task::where('id', $task->id)->delete();
        // ログインユーザーのタスクのみ取得
        $tasks = Task::where('user_id', $user_id)->get();
        if ($item) {
            return response()->json([
                'data' => $tasks
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }
    }
}
