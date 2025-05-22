$(document).ready(function() {
    
    $.easing.easeInOutQuad = function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t + b;
        return -c/2 * ((--t)*(t-2) - 1) + b;
    };
    
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            
            // Calculate navbar height for offset
            var navbarHeight = $('.navbar').outerHeight();
            var targetPosition = target.offset().top - navbarHeight - 10; 
            
            $('html, body').stop().animate({
                scrollTop: targetPosition
            }, 800, 'swing');
            
            if ($(window).width() < 992) {
                $('.navbar-collapse').collapse('hide');
                $('body').removeClass('navbar-open');
            }
        }
    });


    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        var navbarHeight = $('.navbar').outerHeight();
        
        if (scroll >= 50) {
            $('.navbar').addClass('scrolled').css({
                'background-color': 'rgba(13, 110, 253, 0.95)',
                'backdrop-filter': 'blur(10px)',
                'box-shadow': '0 2px 10px rgba(0,0,0,0.1)'
            });
        } else {
            $('.navbar').removeClass('scrolled').css({
                'background-color': '',
                'backdrop-filter': '',
                'box-shadow': ''
            });
        }
        

        if (scroll > 300) {
            $('#scrollToTop').fadeIn();
        } else {
            $('#scrollToTop').fadeOut();
        }

        var sections = $('section[id]');
        var current = '';
        
        sections.each(function() {
            var sectionTop = $(this).offset().top - navbarHeight - 100;
            var sectionHeight = $(this).outerHeight();
            
            if (scroll >= sectionTop && scroll < sectionTop + sectionHeight) {
                current = $(this).attr('id');
            }
        });
        
 
        $('.navbar-nav .nav-link').removeClass('active');
        if (current) {
            $('.navbar-nav .nav-link[href="#' + current + '"]').addClass('active');
        }
    });

    $('.filter-btn').on('click', function(e) {
        e.preventDefault();
        
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        var filterValue = $(this).data('filter');
        var categoryName = $(this).text().trim();

        var navbarHeight = $('.navbar').outerHeight();
        var menuSection = $('#menu');
        var targetPosition = menuSection.offset().top - navbarHeight - 20;
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        
        $('body').addClass('scroll-animation');
        
        $('html, body').animate({
            scrollTop: targetPosition
        }, 800, 'easeInOutQuad', function() {
            $('body').removeClass('scroll-animation');
            
            $btn.html(originalText);
            
            if (filterValue === 'all') {
                $('.menu-item').removeClass('fade-out').addClass('fade-in').show();
                updateMenuCounter('Semua Menu');
            } else {
                $('.menu-item').removeClass('fade-in').addClass('fade-out');
                
                setTimeout(function() {
                    $('.menu-item').hide(); 
                    var $filteredItems = $('.menu-item[data-category="' + filterValue + '"]');
                    $filteredItems.show().removeClass('fade-out').addClass('fade-in');
                    updateMenuCounter(categoryName, $filteredItems.length);
                }, 300);
            }
            
            $('#menu-container').addClass('animate-bounce');
            setTimeout(function() {
                $('#menu-container').removeClass('animate-bounce');
            }, 600);
            
            showFilterNotification(categoryName);
        });
    });
    
    function updateMenuCounter(categoryName, count = null) {
        var totalItems = $('.menu-item').length;
        var displayCount = count !== null ? count : totalItems;
        var counterText = count !== null ? 
            `Menampilkan ${displayCount} menu dari kategori "${categoryName}"` :
            `Menampilkan semua ${totalItems} menu`;
        
        $('.menu-counter').remove();
        
        var $counter = $(`
            <div class="menu-counter alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-filter me-2"></i>${counterText}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('#menu-container').before($counter);
        
        setTimeout(function() {
            $counter.fadeOut();
        }, 3000);
    }
    
    function showFilterNotification(categoryName) {
        var message = categoryName === 'Semua Menu' ? 
            'Menampilkan semua menu' : 
            `Filter aktif: ${categoryName}`;
            
        showToast(message, 'success');
    }

    $('.order-btn').on('click', function() {
        var menuName = $(this).data('menu');
        var menuPrice = $(this).data('harga');
        var formattedPrice = 'Rp ' + number_format(menuPrice, 0, ',', '.');
        
        $('#modal-menu-name').text(menuName);
        $('#modal-menu-price').text(formattedPrice);
        
        var waMessage = encodeURIComponent(
            'Halo, saya ingin memesan:\n\n' +
            '🍜 Menu: ' + menuName + '\n' +
            '💰 Harga: ' + formattedPrice + '\n\n' +
            'Mohon informasi untuk proses pemesanan. Terima kasih!'
        );
        
        var waNumber = '6282395136466'; // Nomor wa
        var waUrl = 'https://wa.me/' + waNumber + '?text=' + waMessage;
        
        $('#confirm-order').attr('href', waUrl);
        
        $('#orderModal').modal('show');
    });

    $('.btn').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.html();
        
        if ($btn.hasClass('order-btn') || $btn.attr('target') === '_blank') {
            return;
        }
        
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        $btn.prop('disabled', true);
        
        setTimeout(function() {
            $btn.html(originalText);
            $btn.prop('disabled', false);
        }, 2000);
    });

    function animateOnScroll() {
        $('.animate-on-scroll').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animated');
            }
        });
    }
    
    $(window).on('scroll', animateOnScroll);
    animateOnScroll();

    $('.menu-card').hover(
        function() {
            $(this).find('.card-img-top').css('transform', 'scale(1.05)');
            $(this).css('box-shadow', '0 1rem 3rem rgba(0,0,0,0.175)');
        },
        function() {
            $(this).find('.card-img-top').css('transform', 'scale(1)');
            $(this).css('box-shadow', '');
        }
    );

    function validateForm(formId) {
        var isValid = true;
        $('#' + formId + ' [required]').each(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        return isValid;
    }

    
    function number_format(number, decimals, dec_point, thousands_sep) {
        decimals = decimals || 0;
        dec_point = dec_point || '.';
        thousands_sep = thousands_sep || ',';
        
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = thousands_sep,
            dec = dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    $('body').append('<button id="scrollToTop" class="scroll-to-top"><i class="fas fa-arrow-up"></i></button>');
    
    $('#scrollToTop').on('click', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 600);
    });

    function hidePreloader() {
        $('#preloader').fadeOut(500, function() {
            $(this).remove();
        });
    }
    
    $(window).on('load', function() {
        hidePreloader();
    });
    
    setTimeout(hidePreloader, 3000);

    $('.navbar-toggler').on('click', function() {
        setTimeout(function() {
            if ($('.navbar-collapse').hasClass('show')) {
                $('body').addClass('navbar-open');
            } else {
                $('body').removeClass('navbar-open');
            }
        }, 350);
    });

    $('.navbar-nav .nav-link').on('click', function() {
        if ($(window).width() < 992) {
            $('.navbar-collapse').collapse('hide');
            $('body').removeClass('navbar-open');
        }
    });

    function lazyLoadImages() {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => imageObserver.observe(img));
    }
    
    lazyLoadImages();

    console.log('%c🍜 Website Mie Ayam 🍜', 'color: #0d6efd; font-size: 20px; font-weight: bold;');
    console.log('%cDeveloped with ❤️', 'color: #198754; font-size: 14px;');
    console.log('%cPowered by Bootstrap 5 & jQuery', 'color: #6c757d; font-size: 12px;');

    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
    });

    $(window).on('load', function() {
        var loadTime = window.performance.timing.domContentLoadedEventEnd - window.performance.timing.navigationStart;
        console.log('Page Load Time: ' + loadTime + 'ms');
    });

});

function updateWhatsAppNumber(newNumber) {
    window.whatsappNumber = newNumber;
}

function showToast(message, type = 'success') {
    var toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    var toastContainer = $('#toast-container');
    if (toastContainer.length === 0) {
        $('body').append('<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
        toastContainer = $('#toast-container');
    }
    
    var toastElement = $(toastHtml);
    toastContainer.append(toastElement);
    
    var toast = new bootstrap.Toast(toastElement[0]);
    toast.show();
    
    toastElement.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Teks berhasil disalin!', 'success');
    }).catch(function() {
        showToast('Gagal menyalin teks!', 'danger');
    });
}