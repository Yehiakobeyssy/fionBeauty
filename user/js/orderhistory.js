$(function(){
   let currentPage = 1;
let totalPages = 1;

function loadOrders(page = 1, search = "") {
    $.ajax({
        url: "ajaxuser/ordershistory.php",
        type: "POST",
        data: { page: page, search: search },
        dataType: "json",
        success: function(response) {
            $("#tblorders").empty();
            if (response.orders.length > 0) {
                $.each(response.orders, function(index, order) {
                    let statusClass = "";
                    switch (order.status) {
                        case 'Pending': statusClass = 'alert alert-info p-1 m-0'; break;
                        case 'Paid': statusClass = 'alert alert-light p-1 m-0'; break;
                        case 'Shipped': statusClass = 'alert alert-primary p-1 m-0'; break;
                        case 'Delivered': statusClass = 'alert alert-success p-1 m-0'; break;
                        case 'Cancelled': statusClass = 'alert alert-danger p-1 m-0'; break;
                        default: statusClass = 'alert alert-secondary p-1 m-0';
                    }

                    $("#tblorders").append(`
                        <tr>
                            <td>${order.invoiceCode}</td>
                            <td>${order.date}</td>
                            <td><strong>$${order.total}</strong> (${order.items} items)</td>
                            <td><div class="${statusClass}">${order.status}</div></td>
                            <td><a href="orderhistory.php?do=detail&id=${order.invoiceID}">View -></a></td>
                        </tr>
                    `);
                });

                currentPage = response.currentPage;
                totalPages = response.totalPages;

                buildPagination();
            } else {
                $("#tblorders").html("<tr><td colspan='5'>No orders found</td></tr>");
                $("#pagination").html(""); // تفريغ الباجينيشن لو مافي نتائج
            }
        }
    });
}

function buildPagination() {
    let pagination = "";
    if (totalPages > 1) {
        // زر Prev
        pagination += `<button class="prev" data-page="${currentPage - 1}" ${currentPage === 1 ? "disabled" : ""}>&lt;</button>`;

        // الأرقام
        for (let i = 1; i <= totalPages; i++) {
            pagination += `<button class="page-btn ${i === currentPage ? "active" : ""}" data-page="${i}">${i}</button>`;
        }

        // زر Next
        pagination += `<button class="next" data-page="${currentPage + 1}" ${currentPage === totalPages ? "disabled" : ""}>&gt;</button>`;
    }
    $("#pagination").html(pagination);
}

// أول تحميل
loadOrders();

// البحث
$("#txtsearchorder").on("keyup", function() {
    currentPage = 1;
    loadOrders(currentPage, $(this).val());
});

// التعامل مع الضغط على أزرار الباجينيشن
$(document).on("click", ".page-btn", function() {
    let page = $(this).data("page");
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        loadOrders(currentPage, $("#txtsearchorder").val());
    }
});
})