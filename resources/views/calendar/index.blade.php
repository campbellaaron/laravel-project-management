@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="container mx-auto">
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-4">Project & Task Calendar</h2>

    <div id="calendar" class="p-4 my-3 mx-2 rounded-md bg-gray-300 dark:bg-slate-700 text-gray-800 dark:text-gray-200 dark:border-"></div>
</div>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap',
            events: '{{ route("calendar.events") }}',
            timeZone: 'local',
            editable: true,
            eventClick: function(info) {
                window.location.href = info.event.url;
            },
            eventDrop: function(info) {
                let eventId = info.event.id;
                let newStart = info.event.start.toISOString();
                let newEnd = info.event.end ? info.event.end.toISOString() : newStart;
                let url = '';

                if (eventId.includes('task_')) {
                    url = '/tasks/' + eventId.split('_')[1] + '/update-date';
                } else if (eventId.includes('project_')) {
                    url = '/projects/' + eventId.split('_')[1] + '/update-dates';
                }

                fetch(`/projects/${eventId.split('_')[1]}/update-dates`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        start_date: newStart,
                        due_date: newEnd
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error("Server error:", data);
                        alert("Failed to update project dates: " + data.error);
                        info.revert(); // Revert event if the update fails
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    alert("Something went wrong updating the project date.");
                    info.revert();
                });
            },
            eventResize: function(info) {
                let eventId = info.event.id;

                if (eventId.includes('project_')) {
                    let newEnd = info.event.end.toISOString(); // Capture new due_date

                    fetch('/projects/' + eventId.split('_')[1] + '/update-due-date', {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ due_date: newEnd })
                    }).then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Failed to update project due date');
                        }
                    });
                }
            }
        });

        calendar.render();
    });

    </script>
@endsection
