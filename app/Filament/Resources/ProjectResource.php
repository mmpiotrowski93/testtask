<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use App\Models\Project;
use App\Traits\TextFilterTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class ProjectResource extends Resource
{
    use TextFilterTrait;

    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('general.projects');
    }

    public static function getPluralModelLabel(): string
    {
        return __('general.projects');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('general.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('general.description'))
                    ->required()
                    ->rows(3),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('general.start_date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('general.end_date')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('general.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('general.start_date'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('general.end_date'))
                    ->sortable(),
            ])
            ->filters([
                static::makeTextFilter('name', __('general.name')),
                static::makeTextFilter('start_date', __('general.start_date')),
                static::makeTextFilter('end_date', __('general.end_date')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('general.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('general.delete')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
