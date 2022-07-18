
(function ($) {
    'use strict';
    $(function () {
    var body = $('body');
    var sidebar = $('.sidebar');

        // Add active class to nav-link based on url dynamically
        // Active class can be hard coded directly in html file also as required

        function addActiveClass(element) {
            
              if (element.attr('href').indexOf(current) !== -1) {
                    element.parents('.nav-item').last().addClass('active');
                    if (element.parents('.sub-menu').length) {
                        element.closest('.collapse').addClass('show');
       
                    }
                    if (element.parents('.submenu-item').length) {
                        element.addClass('active');

                    }else
                    
                    {

                        element.addClass('activex');
                    }
            
            
            }
        }

        var current = location.pathname.split('/').slice(-1)[0].replace(/^\/|\/$/g, '');
        $('.nav li a', sidebar).each(function () {
            const $this = $(this);
            addActiveClass($this);
        });

        // Close other submenu in sidebar on opening any

        sidebar.on('show.bs.collapse', '.collapse', function () {
        sidebar.find('.collapse.show').collapse('hide');

    

        });

        


        // Change sidebar

        $('[data-toggle="minimize"]').on('click', function () {

            body.toggleClass('sidebar-icon-only');
            sidebar.find('.collapse.show').collapse('hide');

            var state = "true"
            


        });


        // checkbox and radios
        $('.form-check label,.form-radio label').append('<i class="input-helper"></i>');

        $('[data-toggle="offcanvas"]').on('click', function () {
            $('.sidebar-offcanvas').toggleClass('active');
        });
    });

})(jQuery);



