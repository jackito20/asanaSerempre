<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Asana\Client;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd($request->all());
        //$data = \Socialite::with('asana')->stateless()->getUserByToken($request->token);
        $asana_client = Client::accessToken($request->token);
        $projects = $asana_client->get('/projects', []);
        $tasks = $asana_client->get('/projects/' . $projects[5]->id . '/tasks', []);

        return response()->json($tasks);
        //dd($projects);
    }
}
