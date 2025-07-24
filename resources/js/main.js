   /* document.addEventListener('DOMContentLoaded', function () {
        document.body.style.overflow = 'hidden';
        document.getElementById('loading').classList.remove('d-none');
        document.getElementById('loading').classList.add('d-flex');
    });

    window.addEventListener('load', function () {
        setTimeout(function () {
            let loading = document.getElementById('loading');
            loading.classList.add('fade-out'); // Adiciona a animação
            document.body.style.overflow = '';
            setTimeout(function () {
                loading.classList.remove('d-flex');
                loading.classList.add('d-none');
            }, 400);
        }, 600);
    });*/
    
   /* document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        img.loading = "eager"; // Carrega as imagens imediatamente
    });*/

    // Adiciona Carousel
    document.addEventListener("DOMContentLoaded", function () {
        const myCarouselElement = document.querySelectorAll('.carousel')
        myCarouselElement.forEach(carousel => {
        new bootstrap.Carousel(carousel,{touch: true})
        })
    })
    // Final Adiciona Carousel 


