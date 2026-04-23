<?php 

namespace App\Support\DataTable\Columns;

class ProgressColumn extends Column
{
    public function render($row): string
    {
        $value = $this->resolve($row);
    

        $color = match(true) {
            $value == 0        => 'progress-error',
            $value <= 25       => 'progress-warning',
            $value <= 50       => 'text-blue-400',
            $value <= 70       => 'progress-accent',
            $value <= 100      => 'progress-success',
            default            => 'progress-error'
        };


            return "
                <div class='flex flex-col items-center'>
                    <span class='font-semibold'>{$value}/{$value}</span>
                    <progress class='progress {$color} w-full' value='{$value}' max='100'></progress>
                </div>
            ";
    }
}