<?php

namespace App\Http\Controllers\Api\Homepage;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProjectsController extends HomepageController
{
    public function __construct()
    {
        $this->middleware('can:role-edit-profile');
    }

    /**
     * Displays projects
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
            } elseif ($request->show === 'both') {
                $query = $query->withTrashed();
            }
        }

        $query = $query->orderBy($request->get('sort', 'id'), $request->get('order', 'asc'));

        return $query->get();
    }

    /**
     * Store a new project
     */
    public function store(StoreProjectRequest $request)
    {
        $project = new Project([
            'project' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
        ]);

        $project->save();

        $tags = $request->collect('tags')->map(fn($tag) => Tag::firstOrCreate(['tag' => $tag]));

        $project->tags()->sync($tags->map(fn($model) => $model->getKey()));

        $project->push();

        $this->pageUpdated();

        return $project;
    }

    /**
     * Display a project
     */
    public function show(Project $project)
    {
        return $project;
    }

    /**
     * Update a project
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->project = $request->title;
        $project->description = $request->description;
        $project->url = $request->url;

        $tagIds = $request->collect('tags')->map(fn($tag) => Tag::firstOrCreate(['tag' => $tag])->getKey());

        $project->tags()->sync($tagIds);

        $project->save();

        $this->pageUpdated();

        return $project;
    }

    /**
     * Restores a project
     *
     * @return array
     */
    public function restore(Project $project)
    {
        $project->restore();

        $this->pageUpdated();

        return [
            'success' => __('Project ":project" was restored.', ['project' => $project->project]),
        ];
    }

    /**
     * Remove a project
     */
    public function destroy(Project $project)
    {
        $project->delete();

        $this->pageUpdated();

        return [
            'success' => __('Project ":project" was removed.', ['project' => $project->project]),
        ];
    }
}
