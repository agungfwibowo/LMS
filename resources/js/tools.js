document.addEventListener('alpine:init', () => {
    Alpine.data('counter', (target, duration = 1200) => ({
        current: 0,

        start() {
            const start = performance.now();

            const animate = (now) => {
                const progress = Math.min((now - start) / duration, 1);

                // Ease Out Cubic
                const eased = 1 - Math.pow(1 - progress, 3);

                this.current = Math.round(eased * target).toLocaleString('id-ID');

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    this.current = target.toLocaleString('id-ID');
                }
            };

            requestAnimationFrame(animate);
        }
    }));
});