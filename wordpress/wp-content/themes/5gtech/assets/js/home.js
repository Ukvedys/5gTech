
    document.documentElement.classList.add('js');

    const header = document.querySelector('.g5-site-header');
    const hero = document.querySelector('.hero');
    const heroMedia = document.getElementById('hero-media');
    const heroSlides = hero ? [...hero.querySelectorAll('[data-hero-slide]')] : [];
    const heroDots = hero ? [...hero.querySelectorAll('[data-hero-go]')] : [];
    const heroLabel = document.getElementById('hero-rotator-label');
    const heroCount = document.getElementById('hero-rotator-count');
    const heroProgress = document.getElementById('hero-rotator-progress');
    const bridgeSection = document.querySelector('.bridge');
    const processSection = document.querySelector('.process');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-placeholder-link]').forEach((link) => {
      link.addEventListener('click', (event) => event.preventDefault());
    });

    requestAnimationFrame(() => {
      requestAnimationFrame(() => document.body.classList.add('is-ready'));
    });

    function onScroll() {
      const y = window.scrollY;
      if (header) header.classList.toggle('scrolled', y > 40);
      if (heroMedia && !reducedMotion && y < window.innerHeight * 1.15) {
        heroMedia.style.transform = `translateY(${y * 0.12}px) scale(1.03)`;
      }
      if (bridgeSection) {
        const bridgeRect = bridgeSection.getBoundingClientRect();
        const revealStart = window.innerHeight * .82;
        const revealEnd = window.innerHeight * .22;
        const revealProgress = Math.max(0, Math.min(1, (revealStart - bridgeRect.top) / (revealStart - revealEnd)));
        bridgeSection.style.setProperty('--bridge-reveal', `${revealProgress * 100}%`);
      }
      if (processSection) {
        const processRect = processSection.getBoundingClientRect();
        const processScrollDistance = Math.max(1, processSection.offsetHeight - window.innerHeight);
        const processLineProgress = Math.max(0, Math.min(1, -processRect.top / processScrollDistance));
        processSection.style.setProperty('--process-line-progress', `${processLineProgress * 100}%`);
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (hero && heroMedia && heroSlides.length > 1) {
      const previousButton = hero.querySelector('[data-hero-previous]');
      const nextButton = hero.querySelector('[data-hero-next]');
      let activeSlide = 0;
      let rotationTimer = 0;
      let transitionTimer = 0;
      let slideSwapTimer = 0;
      let isTransitioning = false;

      const applySlide = (nextIndex) => {
        activeSlide = (nextIndex + heroSlides.length) % heroSlides.length;

        heroSlides.forEach((slide, index) => {
          slide.classList.toggle('is-active', index === activeSlide);
        });

        heroDots.forEach((dot, index) => {
          const isActive = index === activeSlide;
          dot.classList.toggle('is-active', isActive);
          dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        const currentSlide = heroSlides[activeSlide];
        const followingSlide = heroSlides[(activeSlide + 1) % heroSlides.length];
        if (heroLabel) heroLabel.textContent = followingSlide.dataset.title || '';
        if (heroCount) {
          heroCount.textContent = `${String(activeSlide + 1).padStart(2, '0')} / ${String(heroSlides.length).padStart(2, '0')}`;
        }
        heroMedia.setAttribute('aria-label', currentSlide.dataset.alt || currentSlide.dataset.title || '');
      };

      const showSlide = (nextIndex) => {
        const normalizedIndex = (nextIndex + heroSlides.length) % heroSlides.length;
        if (normalizedIndex === activeSlide || isTransitioning) return;
        restartProgress();

        if (reducedMotion) {
          applySlide(normalizedIndex);
          return;
        }

        isTransitioning = true;
        hero.classList.remove('is-switching');
        void hero.offsetWidth;
        hero.classList.add('is-switching');

        window.clearTimeout(slideSwapTimer);
        window.clearTimeout(transitionTimer);
        slideSwapTimer = window.setTimeout(() => applySlide(normalizedIndex), 360);
        transitionTimer = window.setTimeout(() => {
          hero.classList.remove('is-switching');
          isTransitioning = false;
        }, 1050);
      };

      const restartProgress = () => {
        if (!heroProgress || reducedMotion) return;
        hero.classList.remove('is-rotating');
        void heroProgress.offsetWidth;
        hero.classList.add('is-rotating');
      };

      const stopRotation = () => {
        if (rotationTimer) window.clearInterval(rotationTimer);
        rotationTimer = 0;
        hero.classList.remove('is-rotating');
      };

      const startRotation = () => {
        if (reducedMotion || rotationTimer || document.hidden) return;
        rotationTimer = window.setInterval(() => showSlide(activeSlide + 1), 7000);
        restartProgress();
      };

      heroDots.forEach((dot) => {
        dot.addEventListener('click', () => {
          showSlide(Number(dot.dataset.heroGo));
          stopRotation();
          startRotation();
        });
      });

      previousButton?.addEventListener('click', () => {
        showSlide(activeSlide - 1);
        stopRotation();
        startRotation();
      });

      nextButton?.addEventListener('click', () => {
        showSlide(activeSlide + 1);
        stopRotation();
        startRotation();
      });

      hero.addEventListener('mouseenter', stopRotation);
      hero.addEventListener('mouseleave', startRotation);
      hero.addEventListener('focusin', stopRotation);
      hero.addEventListener('focusout', () => {
        window.setTimeout(() => {
          if (!hero.contains(document.activeElement)) startRotation();
        }, 0);
      });

      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          stopRotation();
        } else {
          startRotation();
        }
      });

      startRotation();
    }

    const processSteps = [...document.querySelectorAll('.process-step')];
    const progressFill = document.getElementById('progress-fill');
    const progressLabel = document.getElementById('progress-label');
    if ('IntersectionObserver' in window && progressFill && progressLabel) {
      const processTotal = processSteps.length;
      const processObserver = new IntersectionObserver((entries) => {
        const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
        if (!visible) return;
        const step = Number(visible.target.dataset.step);
        progressFill.style.width = `${processTotal ? (step / processTotal) * 100 : 0}%`;
        progressLabel.textContent = `${String(step).padStart(2, '0')} / ${String(processTotal).padStart(2, '0')}`;
      }, { threshold: [0.35, 0.55, 0.75] });
      processSteps.forEach((step) => processObserver.observe(step));
    }

    const revealGroups = [
      { selector: '.intro-grid > .eyebrow, .intro-grid > :last-child', stagger: 90 },
      { selector: '.network-strip img', media: true },
      { selector: '.services-heading', stagger: 0 },
      { selector: '.service-tile', stagger: 65 },
      { selector: '.bridge-copy', stagger: 0 },
      { selector: '.bridge-standard', stagger: 70 },
      { selector: '.process-intro > *', stagger: 90 },
      { selector: '.experience .eyebrow, .experience h2, .experience-copy', stagger: 80 },
      { selector: '.proof', stagger: 60 },
      { selector: '.map-card', media: true },
      { selector: '.equipment-head > div, .equipment-marquee, .equipment-note', stagger: 80 },
      { selector: '.team-head > *, .team-card', stagger: 70 },
      { selector: '.team-summary .team-metric, .team-link', stagger: 55 },
      { selector: '.audiences-head > *', stagger: 90 },
      { selector: '.audience-item', stagger: 70 },
      { selector: '.section-top > *', stagger: 90 },
      { selector: '.news-card', stagger: 70 },
      { selector: '.final-grid > *', stagger: 100 }
    ];

    revealGroups.forEach(({ selector, stagger = 0, media = false }) => {
      document.querySelectorAll(selector).forEach((element, index) => {
        element.classList.add('reveal');
        if (media) element.classList.add('reveal-media');
        element.style.setProperty('--reveal-delay', `${Math.min(index * stagger, 240)}ms`);
      });
    });

    processSteps.forEach((step) => {
      [...step.children].forEach((element, index) => {
        element.classList.add('reveal');
        if (element.classList.contains('step-media')) element.classList.add('reveal-media');
        element.style.setProperty('--reveal-delay', `${Math.min(index * 65, 210)}ms`);
      });
    });

    const revealElements = [...document.querySelectorAll('.reveal')];
    if (reducedMotion || !('IntersectionObserver' in window)) {
      revealElements.forEach((element) => element.classList.add('is-visible'));
    } else {
      const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        });
      }, { threshold: .12, rootMargin: '0px 0px -8% 0px' });
      revealElements.forEach((element) => revealObserver.observe(element));
    }
  
