
$(document).ready(function(){

    let currentPage = 1;
    const perPage = 10;

    // Load first time
    loadOrders();

    // 🔁 Time interval buttons
    $('.interval-buttons button').on('click', function(){
        $('.interval-buttons button').removeClass('active');
        $(this).addClass('active');
        currentPage = 1;
        loadOrders();
    });

    // 🔍 Search input (instant search)
    $('.search-input').on('keyup', function(){
        currentPage = 1;
        loadOrders();
    });

    // 📅 Date change
    $('.date-input').on('change', function(){
        currentPage = 1;
        loadOrders();
    });

    // 🧾 Status change
    $('.status-select').on('change', function(){
        currentPage = 1;
        loadOrders();
    });

    // 📜 Pagination click (delegated)
    $(document).on('click', '.pagination button', function(){
        const page = $(this).data('page');
        if(page){
            currentPage = page;
            loadOrders();
        }
    });

    // 🧠 Main AJAX loader
    function loadOrders(){
        const days = $('.interval-buttons button.active').val();
        const search = $('.search-input').val();
        const selectedDate = $('.date-input').val();
        const status = $('.status-select').val();

        $.ajax({
            url: 'ajaxadmin/orders.php',
            type: 'POST',
            data: { days, search, selectedDate, status },
            dataType: 'json',
            success: function(res){
                if(res.success){
                    renderTable(res.data);
                }
            }
        });
    }

    // 🧩 Render table with pagination
    function renderTable(data){
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = data.slice(start, end);
        const total = data.length;

        let html = '';
        if(pageData.length === 0){
            html = '<tr><td colspan="7" style="text-align:center;">No orders found</td></tr>';
        } else {
            pageData.forEach(order => {
                let statusClass = 'alert alert-secondary p-1 m-0';
                switch (order.statusName) {
                    case 'Paid':
                        statusClass = 'alert alert-info p-1 m-0';
                        break;
                    case 'Processing':
                        statusClass = 'alert alert-light p-1 m-0';
                        break;
                    case 'On the way':
                        statusClass = 'alert alert-primary p-1 m-0';
                        break;
                    case 'Delivered':
                        statusClass = 'alert alert-success p-1 m-0';
                        break;
                    case 'Cancelled':
                        statusClass = 'alert alert-danger p-1 m-0';
                        break;
                    case 'Full Refund':
                        statusClass = 'alert alert-warning p-1 m-0';
                        break;
                }
                const productInfo = order.firstItemName 
                ? `
                <div class="product-info">
                    <img src="../images/items/${order.firstItemPic}" alt="" class="product-img">
                    <div class="product-text">
                        <div class="product-name">${order.firstItemName}</div>
                        <div class="product-count">+${order.totalItems - 1} more Items</div>
                    </div> 
                </div>
                `
                : 'No products';
                html += `
                    <tr class="roworder" data-index="${order.invoiceID}">
                        <td style="color:var(--color-danger)">${order.invoiceCode}</td>
                        <td><div class="client">${order.clientName}<br><span>${order.clientEmail}</span></div></td>
                        <td class="smallinfo">${formatTime(order.invoiceDate)}</td>
                        <td>${productInfo}</td>
                        <td class="smallinfo">$${parseFloat(order.invoiceAmount).toFixed(2)}</td>
                        <td class="smallinfo">$${parseFloat(order.invoiceCommition || 0).toFixed(2)}</td>
                        <td class="smallinfo">${order.transactionNO || '-'}</td>
                        <td><div class="${statusClass}" style="font-size:12px;">${order.statusName}</div></td>
                    </tr>
                `;
            });
        }
        $('#tblorders').html(html);

        // Pagination footer
        renderPagination(total, start, end);
    }

    function renderPagination(total, start, end){
        const totalPages = Math.ceil(total / perPage);
        const showingStart = total === 0 ? 0 : start + 1;
        const showingEnd = end > total ? total : end;

        let html = `<div class="pagination-info">
                        Showing ${showingStart}-${showingEnd} of ${total}
                    </div>
                    <div class="pagination">`;

        for(let i = 1; i <= totalPages; i++){
            html += `<button data-page="${i}" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
        }
        html += `</div>`;

        // Remove old footer and append new one
        $('.pagination-container').remove();
        $('.tbl').append(`<div class="pagination-container">${html}</div>`);
    }

    // 🕒 Format time like Facebook
    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        if (diffSec < 60) return 'Just now';
        if (diffMin < 60) return diffMin + ' min' + (diffMin > 1 ? 's' : '') + ' ago';
        if (diffHour < 24) return diffHour + ' hour' + (diffHour > 1 ? 's' : '') + ' ago';
        if (diffDay < 7) return diffDay + ' day' + (diffDay > 1 ? 's' : '') + ' ago';

        // Older than 7 days → show full date
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('en-GB', options); // e.g. "10 Sep 2025"
    }


    $(document).on('click', '.roworder', function(){
        const orderID = $(this).data('index');
        if (orderID) {
            window.location.href = `manageOrders.php?do=order&orderID=${orderID}`;
        }
    });

    $(document).on('click', '.btn_status', function() {
    var id = $(this).data('invoice');
    var status = $(this).data('status');

    if (status == 3) {
        // Ask PHP for refund amount before confirming
        $.ajax({
            url: 'ajaxadmin/update_status_item.php',
            type: 'POST',
            data: { daitailInvoiceId: id, status: status, checkOnly: 1 }, // flag to only get amount
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    var amount = parseFloat(data.amount);
                    if (confirm('The refund amount will be $' + amount.toFixed(2) + '. Are you sure?')) {
                        // User confirmed, now actually update status and process refund
                        updateStatus(id, status);
                    }
                } else {
                    alert('Error fetching refund amount.');
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    } else {
        // Other statuses just update
        updateStatus(id, status);
    }
});

function updateStatus(id, status) {
    $.ajax({
        url: 'ajaxadmin/update_status_item.php',
        type: 'POST',
        data: { daitailInvoiceId: id, status: status },
        success: function(response) {
            console.log(response);
            location.reload();
        },
        error: function(xhr, status, error) {
            alert('Error: ' + error);
        }
    });
}


    //updateinvoice
    $(document).on('change', '#updateinvoice', function() {
        var id = $(this).data('index');
        var status = $(this).val();

        // Check if status is 6 (refund)
        if (status == 6) {
            var confirmRefund = confirm('This will refund the money. Are you sure?');
            if (!confirmRefund) {
                // If user cancels, reset the select to previous value
                $(this).val($(this).data('prev')); // optional: store previous value in data-prev
                return; // stop the ajax
            }
        }

        $.ajax({
            url: 'ajaxadmin/update_status_inv.php',
            type: 'POST',
            data: { InvoiceId: id, status: status },
            success: function(response) {
                console.log(response); // debug
                location.reload(); // refresh page after update
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });


});

