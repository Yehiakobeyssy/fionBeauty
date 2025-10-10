
$(document).ready(function() {

    const perPage = 10;
    let allClients = [];
    let currentPage = 1;

    // 🔁 Load on page start and on filter change
    loadClients();
    $('#search, #status, #block, #date').on('change keyup', function(){
        loadClients();
    });

    // 🟢 Load clients with filters
    function loadClients() {
        const search = $('#search').val();
        const status = $('#status').val();
        const block = $('#block').val();
        const date = $('#date').val();

        $.ajax({
            url: 'ajaxadmin/allclients.php',
            method: 'GET',
            dataType: 'json',
            data: {
                search: search,
                status: status,
                block: block,
                date: date
            },
            success: function(response) {
                if (response.success) {
                    allClients = response.data;
                    currentPage = 1;
                    renderTable();
                } else {
                    console.error(response.error);
                }
            },
            error: function(err) {
                console.error('AJAX Error:', err);
            }
        });
    }

    // 🟢 Render table with pagination
    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = allClients.slice(start, end);

        let rows = '';
        pageData.forEach(client => {
            const certIcon = client.certificate === "Have"
                ? `<span style="color:green;">&#10004;</span>`
                : `<span style="color:red;">&#10008;</span>`;

            const statusClass = client.status === "Active" ? "alert-success" : "alert-warning";
            const blockClass = client.block === "Blocked" ? "alert-danger" : "";

            rows += `
                <tr>
                    <td>${client.fullName}</td>
                    <td>${client.phone}</td>
                    <td>${client.email}</td>
                    <td>${certIcon}</td>
                    <td>${client.orders}</td>
                    <td>$${client.balance}</td>
                    <td><span class="${statusClass}">${client.status}</span></td>
                    <td><span class="${blockClass}">${client.block}</span></td>
                    <td>
                        <button class="btnview" data-id="${client.clientID}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99996 4.16699C15.1083 4.16699 17.5256 7.59196 18.3765 9.19314C18.6475 9.70299 18.6475 10.2977 18.3765 10.8075C17.5256 12.4087 15.1083 15.8337 9.99996 15.8337C4.89164 15.8337 2.47436 12.4087 1.62339 10.8075C1.35242 10.2977 1.35242 9.70299 1.62339 9.19314C2.47436 7.59196 4.89164 4.16699 9.99996 4.16699ZM5.69692 7.06507C4.31336 7.98178 3.50548 9.20318 3.09512 9.97531C3.09054 9.98393 3.08865 9.98991 3.08783 9.9932C3.08699 9.99654 3.08683 10.0003 3.08683 10.0003C3.08683 10.0003 3.08699 10.0041 3.08783 10.0075C3.08865 10.0107 3.09054 10.0167 3.09512 10.0253C3.50548 10.7975 4.31336 12.0189 5.69692 12.9356C5.1257 12.0998 4.79163 11.0891 4.79163 10.0003C4.79163 8.91158 5.1257 7.90086 5.69692 7.06507ZM14.303 12.9356C15.6866 12.0189 16.4944 10.7975 16.9048 10.0253C16.9094 10.0167 16.9113 10.0107 16.9121 10.0075C16.9126 10.0053 16.913 10.0022 16.913 10.0022L16.9131 10.0003L16.9128 9.99666L16.9121 9.9932C16.9113 9.98991 16.9094 9.98393 16.9048 9.97531C16.4944 9.20318 15.6866 7.98179 14.303 7.06508C14.8742 7.90086 15.2083 8.91158 15.2083 10.0003C15.2083 11.0891 14.8742 12.0998 14.303 12.9356ZM6.4583 10.0003C6.4583 8.04432 8.04396 6.45866 9.99997 6.45866C11.956 6.45866 13.5416 8.04432 13.5416 10.0003C13.5416 11.9563 11.956 13.542 9.99997 13.542C8.04396 13.542 6.4583 11.9563 6.4583 10.0003Z" fill="#667085"/>
                            </svg>
                        </button></td>
                    </tr>
            `;
        });

        $('#tblresult').html(rows);
        renderPagination();
    }

    // 🟢 Pagination
    function renderPagination() {
        const totalRecords = allClients.length;
        const totalPages = Math.ceil(totalRecords / perPage);
        const startRecord = (currentPage - 1) * perPage + 1;
        const endRecord = Math.min(currentPage * perPage, totalRecords);

        let infoHtml = `<div class="showing">Showing ${startRecord}-${endRecord} from ${totalRecords}</div>`;
        let paginationHtml = '<div class="pagination">';
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            paginationHtml += `<button class="page-btn ${activeClass}" data-page="${i}">${i}</button>`;
        }
        paginationHtml += '</div>';

        $('.tblClients').next('.pagination-container').remove();
        $('.tblClients').after(`<div class="pagination-container">${infoHtml}${paginationHtml}</div>`);

        $('.page-btn').on('click', function() {
            currentPage = parseInt($(this).data('page'));
            renderTable();
        });
    }


    $(document).on('click', '.btnview', function() {
        const clientID = $(this).data('id');
        window.location.href = `manageClients.php?do=view&clientID=${clientID}`;
    });
});

