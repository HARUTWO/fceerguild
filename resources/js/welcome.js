// Initialize info slideshow
document.addEventListener('DOMContentLoaded', function() {
    const INFO_SLIDE_DELAY = 5000;
    const infoSlides = document.querySelectorAll('.slideshow .slide');
    let infoSlideIndex = 0;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (infoSlides.length > 0 && !prefersReduced) {
        let intervalId = setInterval(() => {
            infoSlides[infoSlideIndex].classList.remove('active');
            infoSlideIndex = (infoSlideIndex + 1) % infoSlides.length;
            infoSlides[infoSlideIndex].classList.add('active');
        }, INFO_SLIDE_DELAY);

        infoSlides.forEach(slide => {
            slide.addEventListener('mouseenter', () => clearInterval(intervalId));
            slide.addEventListener('focusin', () => clearInterval(intervalId));
            slide.addEventListener('mouseleave', () => {
                intervalId = setInterval(() => {
                    infoSlides[infoSlideIndex].classList.remove('active');
                    infoSlideIndex = (infoSlideIndex + 1) % infoSlides.length;
                    infoSlides[infoSlideIndex].classList.add('active');
                }, INFO_SLIDE_DELAY);
            });
        });
    }
});
