let selectedSubCatID = ''; // 0 = initial load
$(document).ready(function () {
    

    function getUrlParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    const catId = getUrlParam('catid');
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


    let subcategories = [];
    let currentIndex = 0;
    const pageSize = 5;

    const container = document.getElementById('subcategoryContainer');

    function renderSubcategories() {
        container.innerHTML = '';
        if (subcategories.length === 0) return;

        const itemsToRender = Math.min(pageSize, subcategories.length);

        for (let i = 0; i < itemsToRender; i++) {
            const index = (currentIndex + i) % subcategories.length;
            const item = subcategories[index];

            container.innerHTML += `
                <div class="cardsubcat">
                    <div class="edidsub">
                        <a href="manageproducts.php?do=edidSub&subID=${item.subCatID}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.7656 8.1839C21.9372 7.01232 21.9372 5.11283 20.7656 3.94126L20.0585 3.23415C18.8869 2.06258 16.9874 2.06258 15.8158 3.23415L4.42912 14.6209C4.01544 15.0346 3.73174 15.5602 3.61294 16.133L2.80928 20.0082C2.66314 20.7129 3.28683 21.3366 3.9915 21.1904L7.86673 20.3868C8.43957 20.268 8.96519 19.9843 9.37887 19.5706L20.7656 8.1839ZM19.3514 5.35547L18.6443 4.64836C18.2538 4.25784 17.6206 4.25784 17.2301 4.64836L16.1694 5.70906L18.2907 7.83038L19.3514 6.76968C19.7419 6.37916 19.7419 5.74599 19.3514 5.35547ZM16.8765 9.24459L14.7552 7.12327L5.84333 16.0351C5.70544 16.173 5.61087 16.3482 5.57127 16.5391L5.07693 18.9228L7.46061 18.4285C7.65156 18.3889 7.82676 18.2943 7.96466 18.1564L16.8765 9.24459Z" fill="#667085"/>
                            </svg>
                        </a>
                        <a href="manageproducts.php?do=deleteSub&subID=${item.subCatID}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6667 4.64286H4.44449L5.5556 17.5H14.4445L15.5556 4.64286H3.33337M10 7.85714V14.2857M12.7778 7.85714L12.2223 14.2857M7.22226 7.85714L7.77782 14.2857M7.77782 4.64286L8.33337 2.5H11.6667L12.2223 4.64286" stroke="#E01212" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <div class="imgSub">
                        <img src="../images/items/${item.subCatPic}" alt="">
                    </div>
                    <div class="discription">
                        <h4>${item.subCatName}</h4>
                        <label>${item.subCatDiscription}</label>
                        <button class="see-product" data-subcat="${item.subCatID}">
                            See Product
                        </button>
                    </div>
                </div>
            `;
        }
    }

    fetch('ajaxadmin/fetchthisSubcategory.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `catId=${catId}`
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            subcategories = res.data;
            renderSubcategories();
        } else {
            console.error(res.message);
        }
    });

    document.querySelector('.next').addEventListener('click', () => {
        currentIndex += pageSize;
        if (currentIndex >= subcategories.length) {
            currentIndex = 0;
        }
        renderSubcategories();
    });

    document.querySelector('.prev').addEventListener('click', () => {
        currentIndex -= pageSize;
        if (currentIndex < 0) {
            currentIndex = Math.max(subcategories.length - pageSize, 0);
        }
        renderSubcategories();
    });
    $(document).on('click', '.see-product', function () {
        selectedSubCatID = $(this).data('subcat');
        applyFilterManageCat(1); // reload products
    });

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
            subCatID: selectedSubCatID, // ✅ added
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
