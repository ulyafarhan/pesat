<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Isolate]
class ProjectInfoWidget extends Widget
{
    protected string $view = 'filament.widgets.project-info-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;
}
