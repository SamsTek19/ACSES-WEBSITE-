<script>
    /**
     * Rate Limit Handler
     * 
     * This script provides a reusable rate limit countdown timer for auth forms.
     * When a 429 (Too Many Requests) response is received, it:
     * 1. Displays a countdown timer overlay
     * 2. Disables form submission
     * 3. Automatically re-enables when the timer expires
     */
    window.RateLimitHandler = (function() {
        let activeTimer = null;
        let overlay = null;

        /**
         * Create or get the rate limit overlay element
         */
        function getOverlay() {
            if (overlay) return overlay;

            overlay = document.createElement('div');
            overlay.id = 'rate-limit-overlay';
            overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm transition-all duration-300';
            overlay.innerHTML = `
                <div class="mx-4 max-w-md rounded-2xl bg-white p-8 shadow-2xl text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-amber-100">
                        <i class="ri-time-line text-4xl text-amber-600"></i>
                    </div>
                    <h3 id="rate-limit-title" class="text-xl font-bold text-slate-900 mb-2">Too Many Attempts</h3>
                    <p id="rate-limit-message" class="text-slate-600 mb-6">Please wait before trying again.</p>
                    <div class="relative mx-auto mb-6 h-32 w-32">
                        <svg class="h-32 w-32 -rotate-90 transform">
                            <circle cx="64" cy="64" r="56" stroke="#e2e8f0" stroke-width="8" fill="none"/>
                            <circle id="rate-limit-progress" cx="64" cy="64" r="56" stroke="#f59e0b" stroke-width="8" fill="none"
                                stroke-linecap="round" stroke-dasharray="352" stroke-dashoffset="0"
                                class="transition-all duration-1000 ease-linear"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span id="rate-limit-countdown" class="text-4xl font-bold text-slate-900">60</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500">
                        <i class="ri-shield-check-line mr-1"></i>
                        This helps protect your account from unauthorized access
                    </p>
                </div>
            `;
            overlay.style.display = 'none';
            document.body.appendChild(overlay);
            return overlay;
        }

        /**
         * Show the rate limit countdown
         * @param {number} seconds - Number of seconds to wait
         * @param {string} message - Custom message to display
         * @param {string} title - Custom title to display
         */
        function show(seconds, message, title) {
            if (activeTimer) {
                clearInterval(activeTimer);
            }

            const overlay = getOverlay();
            const countdown = document.getElementById('rate-limit-countdown');
            const progress = document.getElementById('rate-limit-progress');
            const messageEl = document.getElementById('rate-limit-message');
            const titleEl = document.getElementById('rate-limit-title');

            if (title) titleEl.textContent = title;
            if (message) messageEl.textContent = message;

            let remaining = seconds;
            const circumference = 2 * Math.PI * 56; // 352

            function updateDisplay() {
                countdown.textContent = remaining;
                const offset = ((seconds - remaining) / seconds) * circumference;
                progress.style.strokeDashoffset = offset;
            }

            updateDisplay();
            overlay.style.display = 'flex';

            // Disable all form submit buttons
            document.querySelectorAll('form button[type="submit"], form button:not([type])').forEach(btn => {
                btn.disabled = true;
                btn.dataset.rateLimited = 'true';
            });

            activeTimer = setInterval(() => {
                remaining--;
                updateDisplay();

                if (remaining <= 0) {
                    hide();
                }
            }, 1000);
        }

        /**
         * Hide the rate limit overlay and re-enable forms
         */
        function hide() {
            if (activeTimer) {
                clearInterval(activeTimer);
                activeTimer = null;
            }

            if (overlay) {
                overlay.style.display = 'none';
            }

            // Re-enable all form submit buttons
            document.querySelectorAll('[data-rate-limited="true"]').forEach(btn => {
                btn.disabled = false;
                delete btn.dataset.rateLimited;
            });
        }

        /**
         * Handle a 429 response from fetch/axios
         * @param {Response|Object} response - The response object
         * @returns {boolean} - True if it was a rate limit response
         */
        async function handleResponse(response) {
            if (response.status === 429) {
                let data = {};
                try {
                    if (response.json) {
                        data = await response.json();
                    } else if (response.data) {
                        data = response.data;
                    }
                } catch (e) {
                    // If we can't parse, use defaults
                }

                const seconds = data.retry_after || 60;
                const message = data.message || 'Please wait before trying again.';
                
                show(seconds, message);
                return true;
            }
            return false;
        }

        /**
         * Create a fetch wrapper that handles rate limits
         */
        function createFetchWrapper() {
            const originalFetch = window.fetch;
            window.fetch = async function(...args) {
                const response = await originalFetch.apply(this, args);
                
                // Clone response for rate limit check (since body can only be read once)
                if (response.status === 429) {
                    const cloned = response.clone();
                    await handleResponse(cloned);
                }
                
                return response;
            };
        }

        /**
         * Initialize the rate limit handler
         * Call this once on page load
         */
        function init() {
            getOverlay(); // Pre-create overlay

            // Handle form submissions that return 429
            document.addEventListener('submit', async function(e) {
                const form = e.target;
                if (!form.matches('form[data-auth-form], form[method="POST"]')) return;

                // For AJAX forms, the fetch wrapper will handle it
                // For regular forms, we need to intercept the response
            });

            // Optional: Wrap fetch for automatic handling
            // createFetchWrapper();
        }

        return {
            show,
            hide,
            handleResponse,
            init,
            createFetchWrapper
        };
    })();

    // Auto-initialize on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        window.RateLimitHandler.init();
    });
</script>
