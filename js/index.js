$(document).ready(function() {

    /*********************
     * SLIDER LOGIC
     *********************/
    let slideIndex = 0;
    let slides = $(".mySlide");
    let totalSlides = slides.length;

    function showSlides() {
        slides.hide();
        slideIndex++;
        if (slideIndex > totalSlides) { slideIndex = 1; }
        slides.eq(slideIndex - 1).fadeIn();
        setTimeout(showSlides, 5000); // 5 seconds per slide
    }
    showSlides();

    /*********************
     * FOR YOU SECTION
     *********************/
    let forYouSection = $(".foryou"); // the "For You" section

    function loadItems(page) {
        let itemsContainer = forYouSection.find(".items_cards");
        itemsContainer.html('<p>Loading...</p>');

        $.getJSON("ajax/items.php?page=" + page, function(data) {
            let html = "";
            data.items.forEach(item => {
                let stars = "";
                for (let i = 1; i <= 5; i++) {
                    stars += `<i class="fa${i <= Math.round(item.rating) ? "s" : "r"} fa-star text-warning"></i>`;
                }
                let priceHtml = data.clientActive == 1
                    ? `<h4 class="price">$${parseFloat(item.sellPrice).toFixed(2)}</h4>`
                    : `<div class="stars-block">${'<i class="fas fa-star text-muted"></i>'.repeat(5)}</div>`;

                html += `
                    <div class="card">
                        <img src="images/items/${item.mainpic}" alt="${item.itmName}">
                        <div class="card-body itm_daitail" data-index="${item.itmId}">
                            <h5 class="item-title">
                                <span class="name">${item.itmName}</span>
                                <span class="rating">${stars}</span>
                            </h5>
                            <p>${item.itmDesc}...</p>
                            ${priceHtml}
                            <button class="btn-cart" data-id="${item.itmId}">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                `;
            });

            itemsContainer.html(html);
            forYouSection.data("currentPage", data.page);
            forYouSection.data("totalPages", data.totalPages);
        });
    }

    // Pagination buttons for "For You"
    $("#btnNext").on("click", function(e) {
        e.preventDefault();
        let currentPage = forYouSection.data("currentPage") || 1;
        let totalPages = forYouSection.data("totalPages") || 1;
        let nextPage = currentPage >= totalPages ? 1 : currentPage + 1;
        loadItems(nextPage);
    });

    $("#btnBack").on("click", function(e) {
        e.preventDefault();
        let currentPage = forYouSection.data("currentPage") || 1;
        let totalPages = forYouSection.data("totalPages") || 1;
        let prevPage = currentPage <= 1 ? totalPages : currentPage - 1;
        loadItems(prevPage);
    });

    // Initial load
    loadItems(1);

    /*********************
     * CATEGORIES SECTION
     *********************/
    $.getJSON("ajax/get_categories.php", function(categories) {
        categories.forEach(cat => {
            let sectionHtml = `
                <div class="foryou section_index" data-category="${cat.categoryId}">
                    <div class="sectiontitle"><h4>${cat.catName}</h4></div>
                    <div class="title"><h2>${cat.catName}</h2></div>
                    <div class="items_cards"></div>
                    <div class="pagination">
                        <button class="btnBack">Back</button>
                        <button class="btnNext">Next</button>
                    </div>
                </div>`;
            $("#categories_container").append(sectionHtml);

            loadItemscat(cat.categoryId, 1); // load first page
        });
    });

    // Load items for a specific category
    function loadItemscat(categoryId, page) { 
        let section = $(`.foryou[data-category='${categoryId}']`);
        let itemsContainer = section.find(".items_cards");
        itemsContainer.html('<p>Loading...</p>');

        $.getJSON("ajax/category_items.php", { categoryId: categoryId, page: page }, function(data) {
            let html = "";
            data.items.forEach(item => {
                let stars = "";
                for (let i = 1; i <= 5; i++) {
                    stars += `<i class="fa${i <= Math.round(item.rating) ? "s" : "r"} fa-star text-warning"></i>`;
                }

                let priceHtml = data.clientActive == 1
                    ? `<h4 class="price">$${parseFloat(item.sellPrice).toFixed(2)}</h4>`
                    : `<div class="stars-block">${'<i class="fas fa-star text-muted"></i>'.repeat(5)}</div>`;

                html += `
                    <div class="card">
                        <img src="images/items/${item.mainpic}" alt="${item.itmName}">
                        <div class="card-body itm_daitail" data-index="${item.itmId}">
                            <h5 class="item-title">
                                <span class="name">${item.itmName}</span>
                                <span class="rating">${stars}</span>
                            </h5>
                            <p>${item.itmDesc}...</p>
                            ${priceHtml}
                            <button class="btn-cart" data-id="${item.itmId}">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>`;
            });

            itemsContainer.html(html);
            section.data("currentPage", data.page);
            section.data("totalPages", data.totalPages);
        });
    }

    // Pagination buttons for categories
    $(document).on("click", ".btnNext", function(e) {
        e.preventDefault();
        let section = $(this).closest(".foryou");
        let currentPage = section.data("currentPage") || 1;
        let totalPages = section.data("totalPages") || 1;
        let nextPage = currentPage >= totalPages ? 1 : currentPage + 1;
        loadItemscat(section.data("category"), nextPage);
    });

    $(document).on("click", ".btnBack", function(e) {
        e.preventDefault();
        let section = $(this).closest(".foryou");
        let currentPage = section.data("currentPage") || 1;
        let totalPages = section.data("totalPages") || 1;
        let prevPage = currentPage <= 1 ? totalPages : currentPage - 1;
        loadItemscat(section.data("category"), prevPage);
    });

    $(document).on("click", ".itm_daitail", function(){
        let itmID = $(this).data('index');
        location.href="daitailitem.php?itemid="+itmID;
    });

    $(document).on("click", ".btn-cart", function(e) {
        e.preventDefault();
        e.stopPropagation();

        let itmID = $(this).data('id');

        $.post("ajax/addtocart.php", { itemId: itmID }, function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                if(res.cart_count > 0){
                    $('.cartnumber').show();
                }
                $('#numberofitems').html(res.cart_count);
                alert('✅ Successfully added to cart.')
            } else {
                alert('❌ ' + res.message);
            }
        });
    });
});
