$(document).ready(function(){
    let perPage = 10;
    let currentPage = 1;
    let searchQuery = '';
    let duration = 9999; // default: All Time

    function fetchAdmins(page = 1) {
        $.ajax({
            url: "ajaxadmin/fetchAdmin.php",
            type: "POST",
            data: {
                page: page,
                limit: perPage,
                search: searchQuery,
                duration: duration
            },
            dataType: "json",
                success: function(response) {
                renderAdminsTable(response.data);
                renderAdminsPagination(response.total, page);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    function renderAdminsTable(admins) {
        let html = '';
        if (admins.length === 0) {
            html = `<tr><td colspan="5" style="text-align:center;">No admins found</td></tr>`;
        } else {
            admins.forEach(a => {
                html += `
                    <tr>
                        <td>
                            <strong>${a.fName} ${a.lName}</strong><br>
                            <small>${a.adminEmail}</small><br>
                            <small>${a.phoneNumber}</small>
                        </td>
                        <td>${a.formattedDate}</td>
                        <td>${a.adminRoll ?? '-'}</td>
                        <td>
                            <div class="${a.statusClass}" style="padding:3px 6px;border-radius:5px;display:inline-block;">
                                ${a.statusText}
                            </div>
                            ${a.blockText ? `<div class="${a.blockClass}" style="padding:3px 6px;border-radius:5px;display:inline-block;margin-top:3px;">${a.blockText}</div>` : ''}
                        </td>
                        <td>
                            <button class="btnedid" data-id="${a.adminID}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.8047 5.81991C16.781 4.8436 16.781 3.26069 15.8047 2.28438L15.2155 1.69512C14.2391 0.718813 12.6562 0.718813 11.6799 1.69512L2.19097 11.1841C1.84624 11.5288 1.60982 11.9668 1.51082 12.4442L0.841106 15.6735C0.719324 16.2607 1.23906 16.7805 1.82629 16.6587L5.05565 15.989C5.53302 15.89 5.97103 15.6536 6.31577 15.3089L15.8047 5.81991ZM14.6262 3.46289L14.0369 2.87363C13.7115 2.5482 13.1839 2.5482 12.8584 2.87363L11.9745 3.75755L13.7423 5.52531L14.6262 4.6414C14.9516 4.31596 14.9516 3.78833 14.6262 3.46289ZM12.5638 6.70382L10.796 4.93606L3.36948 12.3626C3.25457 12.4775 3.17577 12.6235 3.14277 12.7826L2.73082 14.769L4.71721 14.3571C4.87634 14.3241 5.02234 14.2453 5.13726 14.1303L12.5638 6.70382Z" fill="#FFAD33"/>
                                </svg>
                            </button>
                            <button class="btndelete" data-id="${a.adminID}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                    <path d="M17.1667 4.64286H4.94449L6.0556 17.5H14.9445L16.0556 4.64286H3.83337M10.5 7.85714V14.2857M13.2778 7.85714L12.7223 14.2857M7.72226 7.85714L8.27782 14.2857M8.27782 4.64286L8.83337 2.5H12.1667L12.7223 4.64286" stroke="#E01212" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#tblmanageAdmin').html(html);
    }

    function renderAdminsPagination(totalRecords, currentPage) {
        const totalPages = Math.ceil(totalRecords / perPage);
        if (totalPages <= 1) {
            $('.pagination-container').remove();
            return;
        }

        const startRecord = (currentPage - 1) * perPage + 1;
        const endRecord = Math.min(currentPage * perPage, totalRecords);

        let infoHtml = `<div class="showing">Showing ${startRecord}-${endRecord} of ${totalRecords}</div>`;
        let paginationHtml = '<div class="pagination">';
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            paginationHtml += `<button class="page-btn ${activeClass}" data-page="${i}">${i}</button>`;
        }
        paginationHtml += '</div>';

        $('.tbladmin').next('.pagination-container').remove();
        $('.tbladmin').after(`<div class="pagination-container">${infoHtml}${paginationHtml}</div>`);

        $('.page-btn').on('click', function () {
            currentPage = parseInt($(this).data('page'));
            fetchAdmins(currentPage);
        });
    }

    // 🔍 Search
    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        currentPage = 1;
        fetchAdmins();
    });

    // 📅 Duration buttons
    $('.duration-btn').on('click', function(){
        $('.duration-btn').removeClass('active');
        $(this).addClass('active');
        duration = parseInt($(this).data('value'));
        currentPage = 1;
        fetchAdmins();
    });

    // First load
    fetchAdmins();

    $(document).on('click','.btnedid',function(){
        let aid = $(this).data('id')
        alert(aid);
    })

    $(document).on('click','.btndelete',function(){
        let aid = $(this).data('id')
        alert(aid);
    })
});