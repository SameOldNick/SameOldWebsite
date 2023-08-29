<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tag;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => 'sometimes|in:active,inactive,both',
            'sort' => 'sometimes|in:id,title,description,url',
            'order' => 'sometimes|in:asc,desc',
        ]);

        $query = Project::query();

        if ($request->has('show')) {
            if ($request->show === 'inactive') {
                $query = $query->onlyTrashed();
            } else if ($request->show === 'both') {
                $query = $query->withTrashed();
            }
        }

        $query = $query->orderBy($request->get('sort', 'id'), $request->get('order', 'asc'));

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = new Project([
            'project' => $request->title,
            'description' => $request->description,
            'url' => $request->url
        ]);

        $project->save();

        $tags = collect($request->tags)->map(fn ($tag) => Tag::firstOrCreate(['tag' => $tag]));

        $project->tags()->sync($tags->map(fn ($model) => $model->getKey()));


        $project->push();


        return $project;
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $project;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->project = $request->title;
        $project->description = $request->description;
        $project->url = $request->url;

        $tagIds = collect($request->tags)->map(fn ($tag) => Tag::firstOrCreate(['tag' => $tag])->getKey());

        $project->tags()->sync($tagIds);

        $project->save();

        return $project;
    }

    /**
     * Restores a project
     *
     * @param Project $project
     * @return array
     */
    public function restore(Project $project)
    {
        $project->restore();

        return [
            'success' => __('Project ":project" was restored.', ['project' => $project->project])
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return [
            'success' => __('Project ":project" was removed.', ['project' => $project->project])
        ];
    }
}
