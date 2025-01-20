<?php

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use function Pest\Livewire\livewire;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('testIndex', function () {
    $this->get(ProjectResource::getUrl('index'))->assertSuccessful();
});

it('testCreate', function () {
    $this->get(ProjectResource::getUrl('create'))->assertSuccessful();
});

it('testEdit', function () {
    $project = Project::factory()->create(['id' => 1]);
    $this->get(ProjectResource::getUrl('edit', ['record' => $project->id]))->assertSuccessful();
});

it('testStore', function () {
    $newData = Project::factory()->make();

    livewire(ProjectResource\Pages\CreateProject::class)
        ->fillForm([
            'name' => $newData->name,
            'description' => $newData->description,
            'start_date' => Carbon::parse($newData->start_date)->format('Y-m-d'),
            'end_date' => optional(Carbon::parse($newData->end_date))->format('Y-m-d'),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Project::class, [
        'name' => $newData->name,
        'description' => $newData->description,
        'start_date' => Carbon::parse($newData->start_date)->format('Y-m-d'),
        'end_date' => optional(Carbon::parse($newData->end_date))->format('Y-m-d'),
    ]);
});

it('testUpdate', function () {
    $project = Project::factory()->create();
    $newData = Project::factory()->make();

    livewire(ProjectResource\Pages\EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'description' => $newData->description,
            'start_date' => Carbon::parse($newData->start_date)->format('Y-m-d'),
            'end_date' => optional(Carbon::parse($newData->end_date))->format('Y-m-d'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($project->refresh())
        ->name->toBe($newData->name)
        ->description->toBe($newData->description)
        ->start_date->toBe(Carbon::parse($newData->start_date)->format('Y-m-d'))
        ->end_date->toBe(optional(Carbon::parse($newData->end_date))->format('Y-m-d'));
});

it('testDestroy', function () {
    $project = Project::factory()->create();

    livewire(ProjectResource\Pages\EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->callAction('delete');

    $this->assertModelMissing($project);
});

it('testRelationManagerIndex', function () {
    $project = Project::factory()
        ->has(Task::factory()->count(11))
        ->create();

    livewire(ProjectResource\RelationManagers\TasksRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => EditProject::class,
    ])
        ->assertSuccessful();
});
