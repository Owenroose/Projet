// Nova Tech Custom JavaScript
$(document).ready(function() {
    // Animation for service boxes
    $('.for_box, .service-box, .product-box').hover(
        function() {
            $(this).css('transform', 'translateY(-5px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Smooth scrolling for anchor links
    $('a[href*="#"]').on('click', function(e) {
        e.preventDefault();

        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 100,
            },
            500,
            'linear'
        );
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
