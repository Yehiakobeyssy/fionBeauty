let currentPage = 1;
const perPage = 5;

$(document).ready(function(){
    fetchWorkshops();

    // 🔍 Filters
    $('#txtdateofworkshop, #txtSearchbar, #txtpaid').on('change keyup', function(){
        currentPage = 1;
        fetchWorkshops();
    });

    // 📄 Pagination click
    $(document).on('click', '.pagination button', function(){
        currentPage = $(this).data('page');
        fetchWorkshops();
    });

    // 📂 Expand clients
    $(document).on('click', '.workshop-row', function(){
        const workshopID = $(this).data('id');
        const nextRow = $(this).next('.client-row');

        if(nextRow.length){
            nextRow.toggle();
        } else {
            fetchClients($(this), workshopID);
        }
    });
});

function fetchWorkshops(){
    $.ajax({
        url: 'ajaxadmin/fetchworkshop.php',
        type: 'POST',
        dataType: 'json',
        data: {
            date: $('#txtdateofworkshop').val(),
            search: $('#txtSearchbar').val(),
            paid: $('#txtpaid').val(),
            page: currentPage
        },
        success: function(res){
            renderWorkshops(res.data);
            renderPagination(res.total, (currentPage-1)*perPage, (currentPage-1)*perPage + res.data.length);
        }
    });
}

function renderWorkshops(data){
    let html = '';
    const today = new Date();

    if(data.length === 0){
        html = `<tr><td colspan="7" style="text-align:center;">No workshops found</td></tr>`;
    } else {
        data.forEach(w => {
            const workshopDate = new Date(w.workshop_date);
            const diffDays = Math.ceil((workshopDate - today) / (1000 * 60 * 60 * 24));

            // default class
            let rowClass = '';
            if (diffDays < 0) {
                rowClass = 'alert alert-danger'; // past
            } else if (diffDays <= 7) {
                rowClass = 'alert alert-warning'; // next 7 days
            }

            html += `
                <tr class="workshop-row ${rowClass}" data-id="${w.id}">
                    <td>${w.title}</td>
                    <td>${formatWorkshopDate(w.workshop_date)}</td>
                    <td>${w.start_time}</td>
                    <td>${w.duration_hours} hrs</td>
                    <td>${w.cost == 0 ? 'Free' : w.cost + ' $'}</td>
                    <td>${w.totalClients}</td>
                    <td>
                        <button class="btnview" data-index="${w.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 4.16699C15.1085 4.16699 17.5258 7.59196 18.3768 9.19314C18.6477 9.70299 18.6477 10.2977 18.3768 10.8075C17.5258 12.4087 15.1085 15.8337 10.0002 15.8337C4.89188 15.8337 2.4746 12.4087 1.62363 10.8075C1.35267 10.2977 1.35267 9.70299 1.62363 9.19314C2.4746 7.59196 4.89188 4.16699 10.0002 4.16699ZM5.69716 7.06507C4.31361 7.98178 3.50572 9.20318 3.09536 9.97531C3.09078 9.98393 3.08889 9.98991 3.08807 9.9932C3.08724 9.99654 3.08708 10.0003 3.08708 10.0003C3.08708 10.0003 3.08724 10.0041 3.08807 10.0075C3.08889 10.0107 3.09078 10.0167 3.09536 10.0253C3.50572 10.7975 4.31361 12.0189 5.69716 12.9356C5.12594 12.0998 4.79188 11.0891 4.79188 10.0003C4.79188 8.91158 5.12594 7.90086 5.69716 7.06507ZM14.3033 12.9356C15.6868 12.0189 16.4947 10.7975 16.905 10.0253C16.9096 10.0167 16.9115 10.0107 16.9123 10.0075C16.9129 10.0053 16.9133 10.0022 16.9133 10.0022L16.9133 10.0003L16.913 9.99666L16.9123 9.9932C16.9115 9.98991 16.9096 9.98393 16.905 9.97531C16.4947 9.20318 15.6868 7.98179 14.3033 7.06508C14.8745 7.90086 15.2085 8.91158 15.2085 10.0003C15.2085 11.0891 14.8745 12.0998 14.3033 12.9356ZM6.45854 10.0003C6.45854 8.04432 8.0442 6.45866 10.0002 6.45866C11.9562 6.45866 13.5419 8.04432 13.5419 10.0003C13.5419 11.9563 11.9562 13.542 10.0002 13.542C8.0442 13.542 6.45854 11.9563 6.45854 10.0003Z" fill="#667085"/>
                            </svg>
                        </button>
                        <button class="btnedit" data-index="${w.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.8047 5.81991C16.781 4.8436 16.781 3.26069 15.8047 2.28438L15.2155 1.69512C14.2391 0.718813 12.6562 0.718813 11.6799 1.69512L2.19097 11.1841C1.84624 11.5288 1.60982 11.9668 1.51082 12.4442L0.841106 15.6735C0.719324 16.2607 1.23906 16.7805 1.82629 16.6587L5.05565 15.989C5.53302 15.89 5.97103 15.6536 6.31577 15.3089L15.8047 5.81991ZM14.6262 3.46289L14.0369 2.87363C13.7115 2.5482 13.1839 2.5482 12.8584 2.87363L11.9745 3.75755L13.7423 5.52531L14.6262 4.6414C14.9516 4.31596 14.9516 3.78833 14.6262 3.46289ZM12.5638 6.70382L10.796 4.93606L3.36948 12.3626C3.25457 12.4775 3.17577 12.6235 3.14277 12.7826L2.73082 14.769L4.71721 14.3571C4.87634 14.3241 5.02234 14.2453 5.13726 14.1303L12.5638 6.70382Z" fill="#FFAD33"/>
                            </svg>
                        </button>
                        <button class="btnDelete" data-index="${w.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6667 4.64286H4.44449L5.5556 17.5H14.4445L15.5556 4.64286H3.33337M10 7.85714V14.2857M12.7778 7.85714L12.2223 14.2857M7.22226 7.85714L7.77782 14.2857M7.77782 4.64286L8.33337 2.5H11.6667L12.2223 4.64286" stroke="#E01212" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#fetchworkshop').html(html);
}
$(document).off('click', '.btnedit').on('click', '.btnedit', function(){
    const wid = $(this).data('index');
    window.location.href = `manageWorkshops.php?do=edit&wid=${wid}`;
});

$(document).off('click', '.btnview').on('click', '.btnview', function(){
    const wid = $(this).data('index');
    window.location.href = `manageWorkshops.php?do=view&wid=${wid}`;
});

$(document).off('click', '.btnDelete').on('click', '.btnDelete', function(){
    const wid = $(this).data('index');
    window.location.href = `manageWorkshops.php?do=delete&wid=${wid}`;
}); 


$(document).on('click', '.refund-all-btn', function() {
    const workshopId = $(this).data('workshop');

    if (!confirm('Are you sure you want to refund all clients and deactivate this workshop?')) return;

    $.ajax({
        url: 'ajaxadmin/refundAll.php',
        method: 'POST',
        data: { workshopId: workshopId },
        beforeSend: function() {
            $('.refund-all-btn').prop('disabled', true).text('Processing...');
        },
        success: function(response) {
            if (response.trim() === 'success') {
                alert('All clients refunded and workshop deactivated.');
                location.reload();
            } else if (response.trim() === 'no_invoices') {
                alert('No invoices found for this workshop.');
            } else {
                alert('Error: ' + response);
            }
        },
        error: function(xhr, status, err) {
            alert('AJAX error: ' + err);
        },
        complete: function() {
            $('.refund-all-btn').prop('disabled', false).text('Refund All');
        }
    });
});


function formatWorkshopDate(dateStr) {
    const date = new Date(dateStr);
    const day = date.getDate();
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", 
                        "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();
    return `${day} ${month} ${year}`;
}
function fetchClients(row, workshopID){
    $.ajax({
        url: 'ajaxadmin/fetch_clients.php',
        type: 'POST',
        dataType: 'json',
        data: { workshopID },
        success: function(res){
            let html = `
                <tr class="client-row">
                    <td colspan="7">
                        <div class="client-details">
                            <table class="client-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Client Info</th>
                                        <th>Invoice Code</th>
                                        <th>Invoice Date</th>
                                        <th>Amount</th>
                                        <th>Transaction ID</th>
                                        <th>Method</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;

            if (res.length === 0) {
                html += `
                    <tr>
                        <td colspan="7" style="text-align:center;">No clients booked for this workshop.</td>
                    </tr>
                `;
            } else {
                res.forEach((c, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <strong>${c.clientFname} ${c.clientLname}</strong><br>
                                📞 ${c.clientPhoneNumber}<br>
                                ✉️ ${c.clientEmail}
                            </td>
                            <td>${c.invoiceCode}</td>
                            <td>${c.invoiceDate}</td>
                            <td>${parseFloat(c.totalAmount).toFixed(2)} $</td>
                            <td>${c.transactionID}</td>
                            <td>${c.method}</td>
                        </tr>
                    `;
                });
            }

            html += `
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            `;

            // Insert under the clicked workshop row
            row.after(html);
        }
    });
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

    $('.pagination-container').remove();
    $('.tbl').append(`<div class="pagination-container">${html}</div>`);
}

$(document).on('click', '.refund-action', function() {
    const btn = $(this);
    const invoiceCode = btn.data('invoice');
    const transactionId = btn.data('transaction');
    const amount = parseFloat(btn.data('amount'));
    const detailID = btn.data('detailid');
    const workshopId = btn.data('workshop');

    const confirmMsg = amount > 0 
        ? `Are you sure you want to refund $${amount} and remove this booking?`
        : 'Are you sure you want to delete this free booking?';

    if (!confirm(confirmMsg)) return;

    $.ajax({
        url: 'ajaxadmin/refundWorkshop.php',
        method: 'POST',
        data: {
            invoiceCode: invoiceCode,
            transactionId: transactionId,
            amount: amount,
            detailID: detailID,
            workshopId: workshopId
        },
        beforeSend: function() {
            btn.prop('disabled', true).text('Processing...');
        },
        success: function(res) {
            if (res.trim() === 'success') {
                alert('Action completed successfully.');
                location.reload();
            } else {
                alert('Error: ' + res);
                btn.prop('disabled', false).text(amount > 0 ? 'Refund' : 'Delete');
            }
        },
        error: function(xhr) {
            alert('AJAX Error: ' + xhr.status);
            btn.prop('disabled', false);
        }
    });
});