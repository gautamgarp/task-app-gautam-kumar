<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;
        
        $statuses = ['pending', 'in_progress', 'completed'];
        $titles = [
            'Complete project documentation',
            'Fix login bug',
            'Design new dashboard',
            'Update API endpoints',
            'Review pull requests',
            'Optimize database queries',
            'Deploy to production',
            'Setup monitoring alerts'
        ];
        
        return [
            'title' => $titles[$counter - 1] ?? 'Task ' . $counter,
            'description' => 'This is a sample task description for testing purposes.',
            'status' => $statuses[($counter - 1) % count($statuses)],
            'due_date' => now()->addDays($counter),
        ];
    }
}
