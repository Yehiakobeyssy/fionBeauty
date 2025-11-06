let currentPage = 1;
const perPage = 10;

function loadPayments() {
    const search = $('#txtSearchbar').val();
    const status = $('#txtpaid').val();
    const date = $('#txtdateofinvoice').val();
    const timeinterval = $('.Activetime').data('time');

    $.ajax({
        url: 'ajaxadmin/fetchpayments.php',
        type: 'POST',
        dataType: 'json',
        data: {
            page: currentPage,
            search: search,
            status: status,
            date: date,
            timeinterval: timeinterval
        },
        success: function(res) {
            renderTable(res.data);
            renderPagination(res.total, res.start, res.start + res.perPage);
        }
    });
}

function renderTable(data) {
    let html = '';
    if (data.length === 0) {
        html = `<tr><td colspan="9" class="text-center">No records found</td></tr>`;
    } else {
        data.forEach(order => {
            let statusClass = 'alert alert-secondary p-1 m-0';
            switch (order.statusName) {
                case 'Paid': statusClass = 'alert alert-info p-1 m-0'; break;
                case 'Processing': statusClass = 'alert alert-light p-1 m-0'; break;
                case 'On the way': statusClass = 'alert alert-primary p-1 m-0'; break;
                case 'Delivered': statusClass = 'alert alert-success p-1 m-0'; break;
                case 'Cancelled': statusClass = 'alert alert-danger p-1 m-0'; break;
                case 'Full Refund': statusClass = 'alert alert-warning p-1 m-0'; break;
            }

            // Determine the URL based on type
            let viewUrl = '';
            if(order.type === 'Workshop'){
                viewUrl = `manageWorkshops.php?do=view&wid=${order.invoiceID}`;
            } else { // Order
                viewUrl = `manageOrders.php?do=order&orderID=${order.invoiceID}`;
            }

            html += `
                <tr>
                    <td style="color:var(--color-danger)">${order.invoiceCode}</td>
                    <td class="smallinfo">${order.type}</td>
                    <td class="smallinfo">${formatTime(order.invoiceDate)}</td>
                    <td class="smallinfo">
                        <strong>${order.fullname}</strong><br>
                        ${order.clientPhoneNumber}<br>
                        ${order.clientEmail}
                    </td>
                    <td>$${parseFloat(order.amount).toFixed(2)}</td>
                    <td class="smallinfo">${order.transactionID || '-'}</td>
                    <td class="smallinfo">${order.paymentMethod}</td>
                    <td><span class="${statusClass}">${order.statusName}</span></td>
                    <td>
                        <a href="${viewUrl}" class="btn btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="12" viewBox="0 0 18 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.99996 0.166992C14.1083 0.166992 16.5256 3.59196 17.3765 5.19314C17.6475 5.70299 17.6475 6.29766 17.3765 6.80751C16.5256 8.40869 14.1083 11.8337 8.99996 11.8337C3.89164 11.8337 1.47436 8.40869 0.623389 6.80751C0.352425 6.29766 0.352425 5.70299 0.62339 5.19314C1.47436 3.59196 3.89164 0.166992 8.99996 0.166992ZM4.69692 3.06507C3.31336 3.98178 2.50548 5.20318 2.09512 5.97531C2.09054 5.98393 2.08865 5.98991 2.08783 5.9932C2.08699 5.99654 2.08683 6.00033 2.08683 6.00033C2.08683 6.00033 2.08699 6.00411 2.08783 6.00745C2.08865 6.01074 2.09054 6.01672 2.09512 6.02534C2.50548 6.79747 3.31336 8.01887 4.69692 8.93558C4.1257 8.09979 3.79163 7.08907 3.79163 6.00033C3.79163 4.91158 4.1257 3.90086 4.69692 3.06507ZM13.303 8.93557C14.6866 8.01887 15.4944 6.79747 15.9048 6.02534C15.9094 6.01672 15.9113 6.01074 15.9121 6.00745C15.9126 6.00529 15.913 6.00223 15.913 6.00223L15.9131 6.00033L15.9128 5.99666L15.9121 5.9932C15.9113 5.98991 15.9094 5.98393 15.9048 5.97531C15.4944 5.20318 14.6866 3.98179 13.303 3.06508C13.8742 3.90086 14.2083 4.91158 14.2083 6.00033C14.2083 7.08907 13.8742 8.09979 13.303 8.93557ZM5.4583 6.00033C5.4583 4.04432 7.04396 2.45866 8.99997 2.45866C10.956 2.45866 12.5416 4.04432 12.5416 6.00033C12.5416 7.95633 10.956 9.54199 8.99997 9.54199C7.04396 9.54199 5.4583 7.95633 5.4583 6.00033Z" fill="#667085"/>
                            </svg>
                        </a>
                    </td>
                </tr>`;
        });
    }
    $('table tbody').html(html);
}
function renderPagination(total, start, end, totalAll){
    const totalPages = Math.ceil(total / perPage);
    const showingStart = total === 0 ? 0 : start + 1;
    const showingEnd = end > total ? total : end;
    const overall = totalAll ?? total; // fallback in case totalAll is undefined

    let html = `
        <div class="pagination-info">
            Showing ${showingStart}-${showingEnd} of ${total} 
           
        </div>
        <div class="pagination">`;

    for(let i = 1; i <= totalPages; i++){
        html += `<button data-page="${i}" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
    }

    html += `</div>`;

    $('.pagination-container').remove();
    $('.tbl').after(`<div class="pagination-container">${html}</div>`);
}

// pagination click
$(document).on('click', '.pagination button', function() {
    currentPage = parseInt($(this).data('page'));
    loadPayments();
});

// search + filters
$('#txtSearchbar, #txtdateofinvoice, #txtpaid').on('change keyup', function() {
    currentPage = 1;
    loadPayments();
});
$('#txtSearchbar, #txtdateofinvoice, #txtpaid').on('change keyup', function() {
    currentPage = 1;
    loadPayments();
});

$('.timeinterval .times span').on('click', function() {
    $('.timeinterval .times span').removeClass('Activetime');
    $(this).addClass('Activetime');
    currentPage = 1;
    loadPayments();
});
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
// initial load
$(document).ready(() => {
    loadPayments();
});
