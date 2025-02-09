@php
    $totalSeconds = max(0, $task->totalTrackedTime()); // Ensure no negatives
    $hours = intdiv($totalSeconds, 3600);
    $minutes = intdiv($totalSeconds % 3600, 60);
    $seconds = $totalSeconds % 60;
@endphp
{{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
