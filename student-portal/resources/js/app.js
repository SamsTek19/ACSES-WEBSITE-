import './bootstrap';
import Alpine from 'alpinejs';
import courseRegistrationBulkModule from './modules/adminCourseRegistrationBulk';
import adminSuggestionBulk from './modules/adminSuggestionBulk';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

window.Alpine = Alpine;
Alpine.data('courseRegistrationBulk', courseRegistrationBulkModule);
Alpine.data('adminSuggestionBulk', adminSuggestionBulk);
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const togglePasswordButtons = document.querySelectorAll('[data-password-toggle]');

    togglePasswordButtons.forEach((button) => {
        const targetSelector = button.getAttribute('data-password-toggle');
        if (!targetSelector) {
            return;
        }

        const input = document.querySelector(targetSelector);
        if (!input) {
            return;
        }

        const eyeIcon = button.querySelector('[data-eye]');
        const eyeOffIcon = button.querySelector('[data-eye-off]');

        button.addEventListener('click', () => {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            if (eyeIcon && eyeOffIcon) {
                eyeIcon.classList.toggle('hidden', !isPassword);
                eyeOffIcon.classList.toggle('hidden', isPassword);
            }
        });
    });

    const contextButtons = document.querySelectorAll('.login-context-option');
    const contextInput = document.getElementById('login-context');

    if (contextButtons.length && contextInput) {
        const setState = (btn, state) => {
            btn.dataset.state = state;
            btn.setAttribute('aria-pressed', state === 'active' ? 'true' : 'false');
        };

        contextButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const context = button.getAttribute('data-login-context') ?? 'student';

                contextButtons.forEach((btn) => setState(btn, btn === button ? 'active' : 'inactive'));
                contextInput.value = context;
            });
        });
    }

    const strengthContainers = document.querySelectorAll('[data-password-strength]');

    strengthContainers.forEach((container) => {
        const inputSelector = container.getAttribute('data-password-input');
        const passwordInput = inputSelector ? document.querySelector(inputSelector) : container.querySelector('input');

        if (!passwordInput) {
            return;
        }

        const label = container.querySelector('[data-password-strength-label]');
        const bar = container.querySelector('[data-password-strength-bar]');
        const ruleItems = container.querySelectorAll('[data-password-rule]');

        const evaluateStrength = (value) => {
            if (!value) {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
            }

            const tests = {
                length: value.length >= 8,
                mixed: /[a-z]/.test(value) && /[A-Z]/.test(value),
                number: /\d/.test(value),
                symbol: /[^\w\s]/.test(value),
            };

            let passes = 0;
            ruleItems.forEach((item) => {
                const rule = item.getAttribute('data-password-rule');
                const passed = rule ? Boolean(tests[rule]) : false;
                item.dataset.state = passed ? 'pass' : 'fail';

                if (passed) {
                    item.classList.add('text-emerald-700', 'font-medium');
                    item.classList.remove('text-slate-600');
                } else {
                    item.classList.remove('text-emerald-700', 'font-medium');
                    item.classList.add('text-slate-600');
                }

                const passIcon = item.querySelector('[data-pass-icon]');
                const failIcon = item.querySelector('[data-fail-icon]');
                if (passIcon && failIcon) {
                    passIcon.classList.toggle('hidden', !passed);
                    failIcon.classList.toggle('hidden', passed);
                }

                if (passed) {
                    passes += 1;
                }
            });

            const score = passes / Object.keys(tests).length;

            if (bar) {
                bar.style.width = `${Math.max(score * 100, 8)}%`;

                bar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-[#0b3019]');
                if (score < 0.5) {
                    bar.classList.add('bg-red-500');
                } else if (score < 0.75) {
                    bar.classList.add('bg-yellow-500');
                } else {
                    bar.classList.add('bg-[#0b3019]');
                }
            }

            if (label) {
                label.textContent = score < 0.5 ? 'Weak' : score < 0.75 ? 'Fair' : 'Strong';
            }
        };

        evaluateStrength(passwordInput.value ?? '');
        passwordInput.addEventListener('input', (event) => {
            const target = event.target;
            evaluateStrength(target ? target.value ?? '' : '');
        });
    });

    document.querySelectorAll('[data-numeric-only]').forEach((input) => {
        input.addEventListener('input', () => {
            const sanitized = input.value.replace(/[^\d]/g, '');
            if (sanitized !== input.value) {
                input.value = sanitized;
            }
        });
    });

    const calendarButtons = document.querySelectorAll('[data-calendar-trigger]');

    const launchLink = (href, target = '_blank') => {
        if (!href) {
            return false;
        }

        const anchor = document.createElement('a');
        anchor.href = href;
        anchor.target = target;
        anchor.rel = 'noopener';
        anchor.style.position = 'absolute';
        anchor.style.width = '1px';
        anchor.style.height = '1px';
        anchor.style.overflow = 'hidden';
        document.body.appendChild(anchor);
        anchor.click();
        document.body.removeChild(anchor);

        return true;
    };

    calendarButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const webcal = button.getAttribute('data-calendar-webcal');
            const ics = button.getAttribute('data-calendar-ics');

            const openedWebcal = launchLink(webcal, '_self');

            if (!openedWebcal && ics) {
                launchLink(ics, '_self');
            }
        });
    });

    const otpContainers = document.querySelectorAll('[data-otp-container]');

    otpContainers.forEach((container) => {
        const targetSelector = container.getAttribute('data-otp-target');
        const hiddenInput = targetSelector ? document.querySelector(targetSelector) : null;
        const inputs = Array.from(container.querySelectorAll('[data-otp-input]'));

        if (!inputs.length || !hiddenInput) {
            return;
        }

        const syncValue = () => {
            const value = inputs.map((field) => field.value.replace(/[^\d]/g, '')).join('');
            hiddenInput.value = value;
        };

        const focusInput = (index) => {
            if (index >= 0 && index < inputs.length) {
                inputs[index].focus();
                inputs[index].select();
            }
        };

        const handleInput = (event, index) => {
            const field = event.target;
            const digits = field.value.replace(/[^\d]/g, '');
            field.value = digits.slice(-1);

            if (digits && index < inputs.length - 1) {
                focusInput(index + 1);
            }

            syncValue();
        };

        const handleKeyDown = (event, index) => {
            if (event.key === 'Backspace') {
                event.preventDefault();
                if (event.target.value) {
                    event.target.value = '';
                } else if (index > 0) {
                    inputs[index - 1].value = '';
                }
                syncValue();
                if (index > 0) {
                    focusInput(index - 1);
                }
            }

            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                focusInput(Math.max(index - 1, 0));
            }

            if (event.key === 'ArrowRight') {
                event.preventDefault();
                focusInput(Math.min(index + 1, inputs.length - 1));
            }
        };

        const handlePaste = (event) => {
            event.preventDefault();
            const clipboardData = event.clipboardData?.getData('text') ?? '';
            const digits = clipboardData.replace(/[^\d]/g, '').slice(0, inputs.length).split('');

            if (!digits.length) {
                return;
            }

            inputs.forEach((input, idx) => {
                input.value = digits[idx] ?? '';
            });

            syncValue();
            focusInput(Math.min(digits.length, inputs.length - 1));
        };

        inputs.forEach((input, index) => {
            input.addEventListener('input', (event) => handleInput(event, index));
            input.addEventListener('keydown', (event) => handleKeyDown(event, index));
            input.addEventListener('paste', handlePaste);
        });

        const existingDigits = hiddenInput.value ? hiddenInput.value.replace(/[^\d]/g, '').split('') : [];
        inputs.forEach((input, index) => {
            input.value = existingDigits[index] ?? '';
        });

        if (existingDigits.length) {
            focusInput(Math.min(existingDigits.length, inputs.length - 1));
        } else {
            focusInput(0);
        }

        syncValue();
    });

    const profileForm = document.querySelector('[data-profile-form]');

    if (profileForm) {
        const triggerButton = profileForm.querySelector('[data-avatar-trigger]');
        const removeButton = profileForm.querySelector('[data-avatar-remove]');
        const applyButton = profileForm.querySelector('[data-avatar-apply]');
        const closeButtons = profileForm.querySelectorAll('[data-avatar-cancel]');
        const overlay = profileForm.querySelector('[data-avatar-overlay]');
        const avatarLoadingOverlay = profileForm.querySelector('[data-avatar-loading-overlay]');
        const editorWrapper = profileForm.querySelector('[data-avatar-editor]');
        const editorImage = profileForm.querySelector('[data-avatar-editor-image]');
        const controlsWrapper = profileForm.querySelector('[data-avatar-controls]');
        const fileInput = profileForm.querySelector('[data-avatar-input]');
        const croppedInput = profileForm.querySelector('[data-avatar-cropped]');
        const removeInput = profileForm.querySelector('[data-avatar-remove-input]');
        const previewImage = profileForm.querySelector('[data-avatar-preview]');
        const fallbackInitials = profileForm.querySelector('[data-avatar-fallback]');
        const helperText = profileForm.querySelector('[data-avatar-helper]');
        const defaultHelper = helperText?.textContent ?? '';

        let cropperInstance = null;

        const showAvatarLoading = () => {
            if (!avatarLoadingOverlay) {
                return;
            }

            avatarLoadingOverlay.classList.remove('hidden');
            avatarLoadingOverlay.classList.add('flex');
        };

        const hideAvatarLoading = () => {
            if (!avatarLoadingOverlay) {
                return;
            }

            avatarLoadingOverlay.classList.add('hidden');
            avatarLoadingOverlay.classList.remove('flex');
        };

        const resetCropper = () => {
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
        };

        const setHelper = (message) => {
            if (helperText && message) {
                helperText.textContent = message;
            } else if (helperText) {
                helperText.textContent = defaultHelper;
            }
        };

        const showEditor = () => {
            overlay?.classList.remove('hidden');
            overlay?.classList.add('flex');
            controlsWrapper?.classList.remove('hidden');
            previewImage?.classList.add('hidden');
            fallbackInitials?.classList.add('hidden');
            removeButton?.classList.add('hidden');
        };

        const hideEditor = () => {
            overlay?.classList.add('hidden');
            overlay?.classList.remove('flex');
            controlsWrapper?.classList.add('hidden');
            resetCropper();
        };

        const showPreview = (dataUrl) => {
            const allPreviews = profileForm.querySelectorAll('[data-avatar-preview]');
            const allFallbacks = profileForm.querySelectorAll('[data-avatar-fallback]');
            allPreviews.forEach(img => {
                img.src = dataUrl;
                img.classList.remove('hidden');
            });
            allFallbacks.forEach(fb => fb.classList.add('hidden'));
            removeButton?.classList.remove('hidden');
        };

        const showFallback = () => {
            const allPreviews = profileForm.querySelectorAll('[data-avatar-preview]');
            const allFallbacks = profileForm.querySelectorAll('[data-avatar-fallback]');
            allPreviews.forEach(img => {
                img.src = '';
                img.classList.add('hidden');
            });
            allFallbacks.forEach(fb => fb.classList.remove('hidden'));
            removeButton?.classList.add('hidden');
        };

        triggerButton?.addEventListener('click', () => {
            if (!fileInput) {
                return;
            }

            removeInput && (removeInput.value = '0');
            setHelper('Select a square image (minimum 400px) to continue.');
            fileInput.click();
        });

        fileInput?.addEventListener('change', (event) => {
            const { files } = event.target ?? {};
            const file = files && files[0] ? files[0] : null;

            if (!file) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                setHelper('Unsupported file type. Please choose a PNG or JPG image.');
                event.target.value = '';
                return;
            }

            showAvatarLoading();

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                if (!editorImage || typeof reader.result !== 'string') {
                    hideAvatarLoading();
                    return;
                }

                hideEditor();
                editorImage.src = reader.result;
                showEditor();

                const CropperCtor = typeof Cropper === 'function' ? Cropper : Cropper?.default;

                if (typeof CropperCtor !== 'function') {
                    console.error('Cropper library failed to load.');
                    setHelper('Unable to initialise the cropper. Please refresh and try again.');
                    hideAvatarLoading();
                    return;
                }

                cropperInstance = new CropperCtor(editorImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                });

                setHelper('Adjust the crop to frame your face, then choose "Use crop".');
                hideAvatarLoading();
            });

            reader.readAsDataURL(file);
        });

        applyButton?.addEventListener('click', () => {
            if (!cropperInstance || typeof cropperInstance.getCroppedCanvas !== 'function') {
                setHelper('Still preparing the crop. Please wait a moment and try again.');
                return;
            }

            showAvatarLoading();

            const canvas = cropperInstance.getCroppedCanvas({
                width: 600,
                height: 600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) {
                hideAvatarLoading();
                return;
            }

            const dataUrl = canvas.toDataURL('image/png');
            if (croppedInput) {
                croppedInput.value = dataUrl;
            }
            if (removeInput) {
                removeInput.value = '0';
            }

            showPreview(dataUrl);
            setHelper('Photo ready! Remember to save your changes.');
            hideEditor();
            fileInput && (fileInput.value = '');
            profileForm.requestSubmit();
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                hideEditor();
                setHelper(defaultHelper);
                fileInput && (fileInput.value = '');

                if (previewImage && previewImage.src) {
                    previewImage.classList.remove('hidden');
                    removeButton?.classList.toggle('hidden', !previewImage.src);
                } else {
                    showFallback();
                }
            });
        });

        removeButton?.addEventListener('click', () => {
            hideEditor();
            if (croppedInput) {
                croppedInput.value = '';
            }
            if (removeInput) {
                removeInput.value = '1';
            }
            if (fileInput) {
                fileInput.value = '';
            }

            showFallback();
            setHelper('Photo removed. Save changes to update your profile.');
        });

        profileForm.addEventListener('reset', () => {
            hideEditor();
            showFallback();
            setHelper(defaultHelper);
            if (croppedInput) {
                croppedInput.value = '';
            }
            if (removeInput) {
                removeInput.value = '0';
            }
        });
    }

    // Registration form now renders as a single stage; no multi-step logic required.

    const dropdowns = document.querySelectorAll('[data-dropdown]');

    if (dropdowns.length) {
        dropdowns.forEach((dropdown) => {
            const toggleId = dropdown.querySelector('[data-dropdown-toggle]')?.getAttribute('data-dropdown-toggle');
            if (!toggleId) {
                return;
            }

            const menu = dropdown.querySelector(`#${toggleId}`);
            const toggle = dropdown.querySelector('[data-dropdown-toggle]');

            if (!menu || !toggle) {
                return;
            }

            const closeMenu = () => {
                menu.classList.add('invisible', 'opacity-0');
                menu.classList.remove('visible', 'opacity-100', '-translate-y-0');
            };

            const openMenu = () => {
                menu.classList.remove('invisible', 'opacity-0');
                menu.classList.add('visible', 'opacity-100', '-translate-y-0');
            };

            closeMenu();

            toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = menu.classList.contains('visible');
                if (isOpen) {
                    closeMenu();
                } else {
                    dropdowns.forEach((other) => {
                        if (other !== dropdown) {
                            const otherMenu = other.querySelector('[data-dropdown-menu]');
                            otherMenu?.classList.add('invisible', 'opacity-0');
                            otherMenu?.classList.remove('visible', 'opacity-100', '-translate-y-0');
                        }
                    });
                    openMenu();
                }
            });

            document.addEventListener('click', (event) => {
                if (!dropdown.contains(event.target)) {
                    closeMenu();
                }
            });
        });
    }

    const tipSliders = document.querySelectorAll('[data-tip-slider]');

    if (tipSliders.length) {
        tipSliders.forEach((slider) => {
            const rawTips = slider.getAttribute('data-tip-tips');

            if (!rawTips) {
                return;
            }

            let tips = [];

            try {
                tips = JSON.parse(rawTips);
            } catch (error) {
                console.warn('Unable to parse tip slider data:', error);
            }

            if (!Array.isArray(tips) || tips.length === 0) {
                return;
            }

            const panel = slider.querySelector('[data-tip-panel]');
            const titleEl = slider.querySelector('[data-tip-title]');
            const excerptEl = slider.querySelector('[data-tip-excerpt]');
            const categoryEl = slider.querySelector('[data-tip-category]');
            const publishedEl = slider.querySelector('[data-tip-published]');
            const counterEl = slider.querySelector('[data-tip-counter]');
            const prevBtn = slider.querySelector('[data-tip-prev]');
            const nextBtn = slider.querySelector('[data-tip-next]');
            const dotsContainer = slider.querySelector('[data-tip-dots]');

            if (!panel || !titleEl || !excerptEl || !categoryEl || !publishedEl || !counterEl || !prevBtn || !nextBtn) {
                return;
            }

            const dots = dotsContainer ? Array.from(dotsContainer.querySelectorAll('[data-tip-dot]')) : [];

            let index = 0;
            const autoplayDelay = Number(slider.getAttribute('data-tip-autoplay')) || 0;
            let autoplayHandle = null;

            const render = () => {
                const current = tips[index] || {};

                titleEl.textContent = current.title ?? '';
                excerptEl.textContent = current.excerpt ?? '';
                categoryEl.textContent = current.category ?? 'Security';
                publishedEl.textContent = current.published ?? '';
                counterEl.textContent = `${index + 1}/${tips.length}`;

                if (panel) {
                    panel.classList.remove('opacity-0', 'translate-y-2');
                    panel.classList.add('opacity-100', 'translate-y-0');
                }

                dots.forEach((dot) => {
                    const dotIndex = Number(dot.getAttribute('data-tip-dot'));
                    if (dotIndex === index) {
                        dot.classList.remove('bg-slate-200');
                        dot.classList.add('bg-[#0b3019]');
                    } else {
                        dot.classList.remove('bg-[#0b3019]');
                        dot.classList.add('bg-slate-200');
                    }
                });
            };

            const goTo = (targetIndex) => {
                const newIndex = ((targetIndex % tips.length) + tips.length) % tips.length;

                if (panel) {
                    panel.classList.remove('opacity-100', 'translate-y-0');
                    panel.classList.add('opacity-0', 'translate-y-2');

                    requestAnimationFrame(() => {
                        setTimeout(() => {
                            index = newIndex;
                            render();
                        }, 120);
                    });
                } else {
                    index = newIndex;
                    render();
                }
            };

            const startAutoplay = () => {
                if (!autoplayDelay || tips.length <= 1) {
                    return;
                }

                stopAutoplay();
                autoplayHandle = setInterval(() => {
                    goTo(index + 1);
                }, autoplayDelay);
            };

            const stopAutoplay = () => {
                if (autoplayHandle) {
                    clearInterval(autoplayHandle);
                    autoplayHandle = null;
                }
            };

            prevBtn.addEventListener('click', () => goTo(index - 1));
            nextBtn.addEventListener('click', () => goTo(index + 1));

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    const dotIndex = Number(dot.getAttribute('data-tip-dot'));
                    goTo(dotIndex);
                });
            });

            slider.addEventListener('mouseenter', stopAutoplay);
            slider.addEventListener('mouseleave', startAutoplay);

            render();
            startAutoplay();
        });
    }
});
