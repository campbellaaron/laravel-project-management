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
