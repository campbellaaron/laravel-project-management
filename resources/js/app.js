import './bootstrap';
import $ from 'jquery';
import DataTable from 'datatables.net-dt';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

$(document).ready(function () {
    $('.datatable').each(function () {
        const tableId = $(this).attr('id'); // Get table ID to apply unique settings

        // Destroy existing instance before reinitializing
        if ($.fn.DataTable.isDataTable(this)) {
            $(this).DataTable().destroy();
        }

        // Set default sorting rules for each table
        let orderSettings = [];
        switch (tableId) {
            case 'projectsTable':
                orderSettings = [[4, 'asc']]; // Default: sort by deadline (column 5)
                break;
            case 'tasksTable':
                orderSettings = [[3, 'desc']]; // Default: sort by priority (column 4)
                break;
            case 'usersTable':
                orderSettings = [[0, 'asc']]; // Default: sort by name (column 1)
                break;
            case 'teamsTable':
                orderSettings = [[0, 'asc']]; // Default: sort by team name (column 2)
                break;
            default:
                orderSettings = []; // No default sorting
        }

        // Initialize DataTable with click sorting and default sorting per table
        $(this).DataTable({
            responsive: true,
            order: orderSettings,
            columnDefs: [
                { targets: 'no-sort', orderable: false } // Disable sorting on specific columns
            ]
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {

    appTimerTracker();

});

function appTimerTracker() {
    // Check if we are on the task.show route
    if (!window.location.pathname.match(/^\/tasks\/\d+$/)) {
        return; // Exit script if not on task.show page
    }

    const startButton = document.getElementById("start-timer");
    const stopButton = document.getElementById("stop-timer");
    const timerDisplay = document.getElementById("live-timer");
    const totalTimeDisplay = document.getElementById("total-time");

    let taskId = startButton.getAttribute("data-task-id");
    let timerInterval = null;
    let elapsedSeconds = 0;
    let isRunning = false;

    function formatTime(seconds) {
        let hours = Math.max(0, Math.floor(seconds / 3600));
        let minutes = Math.max(0, Math.floor((seconds % 3600) / 60));
        let secs = Math.max(0, seconds % 60);
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function startLiveTimer() {
        if (!isRunning) {
            isRunning = true;
            timerInterval = setInterval(() => {
                elapsedSeconds++;
                timerDisplay.innerText = formatTime(elapsedSeconds);
            }, 1000);
        }
    }

    function stopLiveTimer() {
        clearInterval(timerInterval);
        isRunning = false;
    }

    function updateTotalTime() {
        fetch(`/tasks/${taskId}/total-time`)
            .then(response => response.json())
            .then(data => {
                if (data.total !== undefined) {
                    totalTimeDisplay.innerText = formatTime(Math.max(0, data.total)); // Ensure no negatives
                }
            })
            .catch(error => console.error("Error:", error));
    }


    // Handle Start Timer
    startButton.addEventListener("click", function () {
        fetch(`/tasks/${taskId}/start-timer`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Content-Type": "application/json"
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                elapsedSeconds = 0;
                startButton.classList.add("hidden");
                stopButton.classList.remove("hidden");
                startLiveTimer();
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Handle Stop Timer
    stopButton.addEventListener("click", function () {
        fetch(`/tasks/${taskId}/stop-timer`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Content-Type": "application/json"
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                stopButton.classList.add("hidden");
                startButton.classList.remove("hidden");
                stopLiveTimer();
                updateTotalTime();
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // 🛠️ ***NEW: Check if task is tracking & restore correct elapsed time**
    fetch(`/tasks/${taskId}/is-tracking`)
        .then(response => response.json())
        .then(data => {
            if (data.is_tracking) {
                startButton.classList.add("hidden");
                stopButton.classList.remove("hidden");

                startTime = Math.floor(data.started_at / 1000); // Convert backend timestamp
                startLiveTimer(); // Resume from correct time
            }
        })
        .catch(error => console.error("Error:", error));
}
