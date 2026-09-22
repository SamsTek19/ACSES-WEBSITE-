@once
    @push('styles')
        <style>
            .skeleton {
                position: relative;
                overflow: hidden;
            }

            .skeleton::after {
                content: '';
                position: absolute;
                inset: 0;
                transform: translateX(-100%);
                background: linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, 0.6) 40%, rgba(255, 255, 255, 0.85) 50%, rgba(255, 255, 255, 0.6) 60%, transparent 100%);
                animation: skeleton-shimmer 1.4s ease-in-out infinite;
            }

            @keyframes skeleton-shimmer {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }
        </style>
    @endpush
@endonce
