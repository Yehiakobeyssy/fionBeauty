$(document).ready(function () {

    // Duration filter active class
    $(".duration-group .duration-btn").on("click", function () {
        $(".duration-group .duration-btn").removeClass("active");
        $(this).addClass("active");
        $("#durationitem").val($(this).data("value"));
        applyFilterManageCat(1); // reload page 1 on filter click
    });

    // Search filter typing
    $("#search").on("keyup", function () {
        applyFilterManageCat(1);
    });

    // Date filter
    $("#date").on("change", function () {
        applyFilterManageCat(1);
    });

    // Initial load
    applyFilterManageCat(1);
});

function applyFilterManageCat(page = 1) {
    function getCatIdFromUrl() {
        let params = new URLSearchParams(window.location.search);
        return params.get("catid") ? parseInt(params.get("catid")) : 0;
    }
    let duration = $("#durationitem").val();
    let search = $("#search").val();
    let date = $("#date").val();
    let catId = getCatIdFromUrl();

    $.ajax({
        url: "ajaxadmin/viewproduct_cat.php",
        type: "POST",
        dataType: "json",
        data: {
            catId: catId,
            duration: duration,
            search: search,
            date: date,
            page: page,
            limit: 12
        },
        success: function (data) {

            // 1️⃣ show products
            $("#product_result").html(data.html);

            // 2️⃣ build pagination
            let pagination = $("#paginationContainer");
            pagination.empty();

            let totalPages = Math.ceil(data.total / data.limit);
            let start = ((data.page - 1) * data.limit) + 1;
            let end = Math.min(data.page * data.limit, data.total);

            // Left side text
            let leftInfo = $(`<div class="pagination-info">Showing ${start}-${end} of ${data.total}</div>`);

            // Right side buttons
            let rightButtons = $('<div class="pagination-buttons"></div>');
            for (let i = 1; i <= totalPages; i++) {
                let btn = $(`<button class="page-btn">${i}</button>`);
                if (i === data.page) btn.attr('disabled', true).addClass('active');
                btn.on('click', function () { applyFilterManageCat(i); });
                rightButtons.append(btn);
            }

            pagination.append(leftInfo).append(rightButtons);
        }
    });
}
