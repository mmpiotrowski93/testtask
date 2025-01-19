<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Traits\TextFilterTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    use TextFilterTrait;
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
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
                Forms\Components\Select::make('status')
                    ->label(__('general.status'))
                    ->options(self::getStatusOptions())
                    ->default('to_do')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label(__('general.assigned_user'))
                    ->relationship('user', 'name')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.status'))
                    ->sortable()
                    ->formatStateUsing(fn (string $state) => self::getStatusOptions()[$state] ?? $state),
            ])
            ->filters([
                self::makeTextFilter('name', __('general.name')),
                self::makeTextFilter('start_date', __('general.start_date')),
                self::makeTextFilter('end_date', __('general.end_date')),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.status'))
                    ->options(self::getStatusOptions()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('general.create')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('general.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('general.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('general.bulk_delete')),
                ]),
            ]);
    }

    private static function getStatusOptions(): array
    {
        return [
            'to_do'       => __('general.to_do'),
            'in_progress' => __('general.in_progress'),
            'completed'   => __('general.completed'),
        ];
    }
}
