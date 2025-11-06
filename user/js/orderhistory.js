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
                    switch (order.status.toLowerCase()) {
                        case 'Paid': statusClass = 'alert alert-info p-1 m-0'; break;
                        case 'processing': statusClass = 'alert alert-light p-1 m-0'; break;
                        case 'on the way': statusClass = 'alert alert-primary p-1 m-0'; break;
                        case 'delivered': statusClass = 'alert alert-success p-1 m-0'; break;
                        case 'cancelled': statusClass = 'alert alert-danger p-1 m-0'; break;
                        case 'full refund': statusClass = 'alert alert-warning p-1 m-0'; break;
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


    // Open the review popup
    $('.btn_toreview').on('click', function() {
        const itemID = $(this).data('index');
        $('#itemID').val(itemID);
        $('#rateScore').val(0); // reset rating
        $('#reviewPopup').fadeIn();
        $('.star-rating .fa-star').removeClass('selected hover');
    });

    // Close popup
    $('#closeReview, #reviewPopup .popup-overlay').on('click', function() {
        $('#reviewPopup').fadeOut();
        $('#reviewForm')[0].reset();
        $('#reviewMessage').html('');
        $('.star-rating .fa-star').removeClass('selected hover');
    });

    // Hover effect
    $('.star-rating .fa-star').hover(
        function() {
            const val = $(this).data('value');
            $('.star-rating .fa-star').each(function(){
                $(this).toggleClass('hover', $(this).data('value') <= val);
            });
        },
        function() {
            $('.star-rating .fa-star').removeClass('hover');
        }
    );

    // Click to select rating
    $('.star-rating .fa-star').on('click', function(){
        const val = $(this).data('value');
        $('#rateScore').val(val);
        $('.star-rating .fa-star').each(function(){
            $(this).toggleClass('selected', $(this).data('value') <= val);
        });
    });

    // Submit form via AJAX
$('#reviewForm').on('submit', function(e) {
    e.preventDefault();

    const rate = $('#rateScore').val();
    if(rate == 0) {
        alert('Please select a star rating before submitting.');
        return; // stop submission
    }

    const formData = $(this).serialize();

    $.ajax({
        url: 'ajaxuser/submitReview.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#reviewMessage').html('<span style="color:green">'+response.message+'</span>');
                setTimeout(function(){
                    $('#reviewPopup').fadeOut();
                    $('#reviewForm')[0].reset();
                    $('#reviewMessage').html('');
                    $('.star-rating .fa-star').removeClass('selected hover');
                }, 1500);
            } else {
                $('#reviewMessage').html('<span style="color:red">'+response.message+'</span>');
            }
        },
        error: function() {
            $('#reviewMessage').html('<span style="color:red">Something went wrong, try again!</span>');
        }
    });
});



})