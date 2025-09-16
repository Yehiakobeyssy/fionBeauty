 $(document).ready(function() {
    let slideIndex = 0;
    let slides = $(".mySlide");
    let total = slides.length;

    function showSlides() {
        slides.hide();
        slideIndex++;
        if (slideIndex > total) { slideIndex = 1; }
        slides.eq(slideIndex - 1).fadeIn();
        setTimeout(showSlides, 5000); // 5 seconds per slide
    }

    showSlides();

    let currentPage = 1;
    let totalPages = 1;
    let clientActive = 0;

    function loadItems(page) {
    $.getJSON("ajax/items.php?page=" + page, function(data) {
        let html = "";
        currentPage = data.page;
        totalPages = data.totalPages;
        clientActive = data.clientActive;

        data.items.forEach(item => {
        // rating stars
        let stars = "";
        for (let i=1; i<=5; i++) {
            stars += `<i class="fa${i <= Math.round(item.rating) ? "s" : "r"} fa-star text-warning"></i>`;
        }

        // price or hidden
        let priceHtml = clientActive == 1
            ? `<h4 class="price">$${parseFloat(item.sellPrice).toFixed(2)}</h4>`
            : `<div class="stars-block">${'<i class="fas fa-star text-muted"></i>'.repeat(5)}</div>`;

        html += `
        <div class="card">
          <img src="images/items/${item.itmId}.jpg" alt="${item.itmName}">
          <div class="card-body">
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

        $(".items_cards").html(html);
    });
    }

    // pagination controls
    $("#btnNext").on("click", function() {
    let nextPage = currentPage >= totalPages ? 1 : currentPage + 1;
    loadItems(nextPage);
    });

    $("#btnBack").on("click", function() {
    let prevPage = currentPage <= 1 ? totalPages : currentPage - 1;
    loadItems(prevPage);
    });

    // first load
    loadItems(1);

}); 
