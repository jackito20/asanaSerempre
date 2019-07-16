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
        $asana_client = Client::accessToken(decrypt(auth()->user()->assana_access));
        $user = $asana_client->users->me();
        $userWorkspaces = array_filter($user->workspaces, function($item) { return $item->name === 'Personal Projects'; });
        $userWorkspaces = $user->workspaces;
        $projects = $asana_client->projects->findByWorkspace($userWorkspaces[0]->id, null, array('iterator_type' => false, 'page_size' => null))->data;

      $projects = $asana_client->get('/projects', []);
      $tasks = $asana_client->get('/projects/' . $projects[4]->id . '/tasks', []);

        var_dump("////----USER---////");
        var_dump($user);
        var_dump("////----WORKSPACES---////");
        var_dump($userWorkspaces);
        var_dump("////----PROJECTS---////");
        var_dump($projects);
        var_dump("////----TASKS---////");
        var_dump($tasks);

        $taskOptions = ['name' => 'task prueba asana 3',
                        'workspace' => $userWorkspaces[0]->id,
                        'projects' =>[$projects[4]->id]]
        $task = $asana_client->POST('/tasks', );

        return response()->json($task);
    }

    public function store(Request $request){

    }
}
