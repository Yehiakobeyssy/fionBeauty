$(document).ready(function(){
    let currentPage = 1;
let totalPages = 1;
const perPage = 5;

// Main loader
function loadTrainings(page = 1, search = "") {
    $.ajax({
        url: "ajaxuser/fetchtraining.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            // Filter by search term
            if (search.trim() !== "") {
                data = data.filter(item =>
                    item.workshop.toLowerCase().includes(search.toLowerCase())
                );
            }

            // Order newest to oldest by Date
            data.sort((a, b) => new Date(b.Date) - new Date(a.Date));

            // Pagination setup
            totalPages = Math.ceil(data.length / perPage);
            const start = (page - 1) * perPage;
            const paginatedData = data.slice(start, start + perPage);

            // Build table
            let html = "";
            paginatedData.forEach(row => {
                const dateDisplay = formatWorkshopDate(row.Date);
                const bookingDisplay = timeAgo(row["Booking Date"]);
                const isOnlineText = row["Is Online"] == 1 ? "Yes" : "No";

                // Check if workshop is in the past
                const isPast = new Date(row.Date) < new Date();
                const rowClass = isPast ? "past" : "";

                let costText = row.Cost == 0 
                                ? `<span style="color: var(--color-primary); font-weight:bold;">Free</span>` 
                                : `$${row.Cost}`;
                html += `
                    <tr class="${rowClass}">
                        <td>${row.workshop}</td>
                        <td>${dateDisplay}</td>
                        <td>${row.Time}</td>
                        <td>${row.Duration} hrs</td>
                        <td>${costText}</td>
                        <td>${isOnlineText}</td>
                        <td>${bookingDisplay}</td>
                        <td><a href="traininghistory.php?do=detail&id=${row.id}" class="btn btn-sm btn-primary">View</a></td>
                    </tr>
                `;
            });

            $("#reultfetch").html(html);
            buildPagination();
        },
        error: function () {
            $("#reultfetch").html("<tr><td colspan='8'>Error loading data.</td></tr>");
        }
    });
}

// Pagination builder
function buildPagination() {
    let pagination = "";
    if (totalPages > 1) {
        pagination += `<button class="prev" data-page="${currentPage - 1}" ${currentPage === 1 ? "disabled" : ""}>&lt;</button>`;
        for (let i = 1; i <= totalPages; i++) {
            pagination += `<button class="page-btn ${i === currentPage ? "active" : ""}" data-page="${i}">${i}</button>`;
        }
        pagination += `<button class="next" data-page="${currentPage + 1}" ${currentPage === totalPages ? "disabled" : ""}>&gt;</button>`;
    }
    $("#pagination").html(pagination);
}

// Handle pagination clicks
$(document).on("click", ".page-btn, .prev, .next", function () {
    const page = $(this).data("page");
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        loadTrainings(currentPage, $("#txtsearchtraining").val());
    }
});

// Search box
$("#txtsearchtraining").on("keyup", function () {
    currentPage = 1;
    loadTrainings(currentPage, $(this).val());
});

// Date display logic
function formatWorkshopDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const diffDays = Math.floor((date - today) / (1000 * 60 * 60 * 24));

    const options = { day: "numeric", month: "short", year: "numeric" };
    const formatted = date.toLocaleDateString("en-GB", options);

    if (diffDays === 0) return "Today";
    else if (diffDays > 0 && diffDays < 15) return `${diffDays} days left`;
    else return formatted;
}

// Facebook-like time for booking date
function timeAgo(dateString) {
    const date = new Date(dateString);
    const seconds = Math.floor((new Date() - date) / 1000);

    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) return interval + " year" + (interval > 1 ? "s" : "") + " ago";
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) return interval + " month" + (interval > 1 ? "s" : "") + " ago";
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) return interval === 1 ? "Yesterday" : interval + " days ago";
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) return interval + " hour" + (interval > 1 ? "s" : "") + " ago";
    interval = Math.floor(seconds / 60);
    if (interval >= 1) return interval + " minute" + (interval > 1 ? "s" : "") + " ago";
    return "Just now";
}

// CSS for past workshops
$("<style>")
    .prop("type", "text/css")
    .html(`
        .past {
            opacity: 0.5;
            text-decoration: line-through;
        }
        #pagination {
            margin-top: 15px;
        }
        #pagination button {
            margin: 0 3px;
            padding: 5px 10px;
        }
        #pagination .active {
            background: #007bff;
            color: white;
            border: none;
        }
    `)
    .appendTo("head");

// Initial load
loadTrainings();

})