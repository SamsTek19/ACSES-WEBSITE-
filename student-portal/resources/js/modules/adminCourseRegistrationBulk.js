export default function courseRegistrationBulkModule() {
    return {
        selectedIds: [],
        allIds: [],
        statusValue: '',
        commentValue: '',

        initialize(ids = []) {
            this.allIds = Array.isArray(ids) ? ids.map(Number) : [];
            const form = this.$refs?.bulkForm;
            const preChecked = form
                ? Array.from(form.querySelectorAll('input[name="ids[]"]:checked')).map((el) => Number(el.value))
                : [];
            this.selectedIds = [...new Set(preChecked)];
        },

        toggle(id, checked) {
            id = Number(id);
            if (checked) {
                if (!this.selectedIds.includes(id)) {
                    this.selectedIds.push(id);
                }
            } else {
                this.selectedIds = this.selectedIds.filter((item) => item !== id);
            }
        },

        toggleAll(checked) {
            if (checked) {
                this.selectedIds = [...this.allIds];
                this.$nextTick(() => {
                    const form = this.$refs?.bulkForm;
                    if (!form) {
                        return;
                    }
                    form.querySelectorAll('input[name="ids[]"]').forEach((checkbox) => {
                        checkbox.checked = true;
                    });
                });
            } else {
                this.selectedIds = [];
                this.$nextTick(() => {
                    const form = this.$refs?.bulkForm;
                    if (!form) {
                        return;
                    }
                    form.querySelectorAll('input[name="ids[]"]').forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                });
            }
        },

        submit(action) {
            if (!this.selectedIds.length) {
                return;
            }

            const form = this.$refs.bulkForm;
            if (!form) {
                return;
            }

            this.$refs.actionInput.value = action;
            this.$refs.statusInput.value = this.statusValue || '';
            this.$refs.commentInput.value = this.commentValue || '';

            const existingHiddenInputs = form.querySelectorAll('input[type="hidden"][name="ids[]"]');
            existingHiddenInputs.forEach((input) => input.remove());

            this.selectedIds.forEach((id) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'ids[]';
                hidden.value = String(id);
                form.appendChild(hidden);
            });

            form.submit();
        },

        get bulkSummary() {
            if (this.selectedIds.length === 0) {
                return 'No registrations selected';
            }

            const total = this.selectedIds.length;
            return `${total} ${total === 1 ? 'registration' : 'registrations'} selected`;
        },

        get allSelected() {
            return this.selectedIds.length > 0 && this.selectedIds.length === this.allIds.length;
        },

        get canApplyStatus() {
            return this.selectedIds.length > 0 && Boolean(this.statusValue);
        },
    };
}
