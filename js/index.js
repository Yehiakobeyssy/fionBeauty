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
}); 
