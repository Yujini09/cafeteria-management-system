import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

const loadingDisableClasses = ['opacity-60', 'cursor-not-allowed'];
const loadingSpinnerMarkup = '<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

const deferSubmitState = (callback) => {
    if (typeof window.queueMicrotask === 'function') {
        window.queueMicrotask(callback);
        return;
    }
    Promise.resolve().then(callback);
};

const markDisabled = (control) => {
    if (!control || control.dataset.cmsLoadingManaged === 'true') return;
    control.dataset.cmsLoadingManaged = 'true';
    control.dataset.cmsWasDisabled = control.disabled ? 'true' : 'false';
    control.disabled = true;
    control.classList.add(...loadingDisableClasses);
    control.setAttribute('aria-disabled', 'true');
};

const unmarkDisabled = (control) => {
    if (!control || control.dataset.cmsLoadingManaged !== 'true') return;
    control.disabled = control.dataset.cmsWasDisabled === 'true';
    control.classList.remove(...loadingDisableClasses);
    if (control.dataset.cmsWasDisabled === 'true') {
        control.setAttribute('aria-disabled', 'true');
    } else {
        control.removeAttribute('aria-disabled');
    }
    delete control.dataset.cmsLoadingManaged;
    delete control.dataset.cmsWasDisabled;
};

const applyLoadingLabel = (control, loadingText) => {
    if (!control || !loadingText) return;
    if (control.tagName === 'BUTTON') {
        if (typeof control.dataset.cmsOriginalHtml === 'undefined') {
            control.dataset.cmsOriginalHtml = control.innerHTML;
        }
        control.innerHTML = `<span class="inline-flex items-center gap-2">${loadingSpinnerMarkup}<span>${loadingText}</span></span>`;
        return;
    }

    if (control.tagName === 'INPUT' && (control.type || '').toLowerCase() === 'submit') {
        if (typeof control.dataset.cmsOriginalValue === 'undefined') {
            control.dataset.cmsOriginalValue = control.value;
        }
        control.value = loadingText;
    }
};

const restoreLoadingLabel = (control) => {
    if (!control) return;
    if (typeof control.dataset.cmsOriginalHtml !== 'undefined') {
        control.innerHTML = control.dataset.cmsOriginalHtml;
        delete control.dataset.cmsOriginalHtml;
    }
    if (typeof control.dataset.cmsOriginalValue !== 'undefined') {
        control.value = control.dataset.cmsOriginalValue;
        delete control.dataset.cmsOriginalValue;
    }
};

const getFormSubmitControls = (form) => {
    if (!form) return [];
    return Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
};

const startButtonLoading = (button, fallbackText = null) => {
    if (!button || button.dataset.cmsLoadingActive === 'true') return false;
    const loadingText = button.dataset.loadingText || fallbackText || 'Processing...';
    markDisabled(button);
    applyLoadingLabel(button, loadingText);
    button.dataset.cmsLoadingActive = 'true';
    button.setAttribute('aria-busy', 'true');
    return true;
};

const stopButtonLoading = (button) => {
    if (!button) return;
    restoreLoadingLabel(button);
    unmarkDisabled(button);
    button.removeAttribute('aria-busy');
    delete button.dataset.cmsLoadingActive;
};

const startFormLoading = (form, submitter = null, fallbackText = null) => {
    if (!form) return false;
    if (form.dataset.cmsSubmitting === 'true') return false;
    form.dataset.cmsSubmitting = 'true';

    const controls = getFormSubmitControls(form);
    controls.forEach((control) => markDisabled(control));

    const target = submitter && form.contains(submitter)
        ? submitter
        : (controls[0] || null);

    if (target) {
        const loadingText = target.dataset.loadingText || form.dataset.loadingText || fallbackText || 'Processing...';
        applyLoadingLabel(target, loadingText);
        target.dataset.cmsLoadingActive = 'true';
        target.setAttribute('aria-busy', 'true');
    }

    return true;
};

const resetFormLoading = (form) => {
    if (!form) return;
    delete form.dataset.cmsSubmitting;
    getFormSubmitControls(form).forEach((control) => {
        restoreLoadingLabel(control);
        unmarkDisabled(control);
        control.removeAttribute('aria-busy');
        delete control.dataset.cmsLoadingActive;
    });
};

const bindActionLoadingForms = (root = document) => {
    if (!root || typeof root.querySelectorAll !== 'function') return;
    const forms = root.querySelectorAll('form[data-action-loading]');
    forms.forEach((form) => {
        if (form.dataset.cmsLoadingBound === 'true') return;
        form.dataset.cmsLoadingBound = 'true';

        form.addEventListener('submit', (event) => {
            if (form.dataset.cmsSubmitting === 'true') {
                event.preventDefault();
                return;
            }

            const submitter = event.submitter || null;
            deferSubmitState(() => {
                if (event.defaultPrevented) return;
                startFormLoading(form, submitter);
            });
        });
    });
};

window.cmsActionButtons = {
    bindForms: bindActionLoadingForms,
    start: startButtonLoading,
    stop: stopButtonLoading,
    startFormSubmit: startFormLoading,
    resetForm: resetFormLoading,
};

document.addEventListener('DOMContentLoaded', () => {
    bindActionLoadingForms(document);
});

document.addEventListener('livewire:navigated', () => {
    bindActionLoadingForms(document);
});

// Register all Alpine data components when Alpine initializes
document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;
    if (!Alpine) return;
    // Notifications panel
    Alpine.data('notificationsPanel', () => ({
        open: false,
        items: [],
        loading: true,
        unreadCount: 0,
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 30000);
        },
        updateUnread() {
            this.unreadCount = this.items.filter(item => !item.read).length;
        },
        markAllRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    this.items = this.items.map(item => ({ ...item, read: true }));
                    this.updateUnread();
                })
                .catch(error => console.error('Error marking notifications read:', error));
        },
        toggleRead(id) {
            const target = this.items.find(item => item.id === id);
            if (!target) return;
            const nextRead = !target.read;
            fetch(`/admin/notifications/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ read: nextRead })
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    this.items = this.items.map(item => item.id === id ? { ...item, read: nextRead } : item);
                    this.updateUnread();
                })
                .catch(error => console.error('Error toggling notification read:', error));
        },
        fetchNotifications() {
            this.loading = true;
            fetch('/admin/recent-notifications', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    const uniqueNotifications = (data || []).reduce((seen, notification) => {
                        if (!seen.find(n => n.id === notification.id)) seen.push(notification);
                        return seen;
                    }, []);
                    this.items = uniqueNotifications.map(notification => {
                        const metadata = notification.metadata || {};
                        const actor = metadata.updated_by || metadata.generated_by || metadata.created_by || metadata.deleted_by || metadata.added_by || metadata.removed_by || (notification.user ? notification.user.name : 'System');
                        return {
                            id: notification.id,
                            actor: actor || 'System',
                            description: notification.description || 'Unknown Action',
                            time: notification.created_at ? new Date(notification.created_at).toLocaleString() : 'Unknown Time',
                            read: Boolean(notification.read),
                        };
                    });
                    this.updateUnread();
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    this.items = [];
                })
                .finally(() => {
                    this.loading = false;
            });
        }
    }));

    // Customer payment prompt modal
    Alpine.data('paymentPrompt', (opts = {}) => ({
        open: false,
        loading: true,
        submitting: false,
        reservation: null,
        totalAmount: 0,
        form: {
            reference_number: opts.oldReference || '',
            department_office: opts.oldDepartment || '',
            payer_name: opts.oldPayer || '',
            account_code: opts.oldAccountCode || ''
        },
        init() {
            if (window.location.pathname.startsWith('/payments/')) {
                this.loading = false;
                return;
            }
            this.fetchDueReservation();
        },
        get actionUrl() {
            return this.reservation ? `/payments/${this.reservation.id}` : '#';
        },
        get formattedAmount() {
            const amount = Number(this.totalAmount || 0);
            return 'PHP ' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        get reservationLabel() {
            if (!this.reservation) return '';
            const id = String(this.reservation.id || '').padStart(6, '0');
            return `${this.reservation.event_name || 'Reservation'} (#${id})`;
        },
        get reservationPeriod() {
            if (!this.reservation) return '';
            const start = this.reservation.event_date || '';
            const end = this.reservation.end_date || '';
            if (start && end && start !== end) return `${start} - ${end}`;
            return start || end || 'Date not set';
        },
        get reservationVenue() {
            return this.reservation?.venue || 'Venue not specified';
        },
        get reservationContact() {
            return this.reservation?.contact_person || 'Contact not specified';
        },
        dismiss() {
            this.open = false;
        },
        submitPayment() {
            if (this.submitting) return;
            if (!this.reservation || !this.reservation.id) {
                return;
            }
            const form = this.$refs?.paymentForm;
            if (!form) return;
            const action = this.actionUrl;
            if (!action || action === '#') return;
            this.submitting = true;
            form.setAttribute('action', action);
            form.submit();
        },
        async fetchDueReservation() {
            this.loading = true;
            try {
                const res = await fetch('/customer/payment-due', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                const data = await res.json();

                if (data && data.reservation) {
                    this.reservation = data.reservation;
                    this.totalAmount = data.total_amount || 0;
                    if (!this.form.department_office) {
                        this.form.department_office = data.reservation.department || '';
                    }
                    if (!this.form.payer_name) {
                        this.form.payer_name = data.reservation.contact_person || '';
                    }
                    if (!this.form.account_code) {
                        this.form.account_code = data.reservation.account_code || '';
                    }
                    this.open = true;
                } else {
                    this.reservation = null;
                    this.open = false;
                }
            } catch (error) {
                console.error('Error loading payment prompt:', error);
                this.reservation = null;
                this.open = false;
            } finally {
                this.loading = false;
            }
        }
    }));

    // Reservation list page
    Alpine.data('reservationList', () => ({
        approveConfirmationOpen: false,
        declineConfirmationOpen: false,
        selectedReservationId: null,
        openApproveConfirmation(id) {
            this.selectedReservationId = id ?? null;
            this.declineConfirmationOpen = false;
            this.approveConfirmationOpen = true;
        },
        openDeclineConfirmation(id) {
            this.selectedReservationId = id ?? null;
            this.approveConfirmationOpen = false;
            this.declineConfirmationOpen = true;
        },
        redirectToShowPage() {
            if (!this.selectedReservationId) return;
            window.location.href = `/admin/reservations/${this.selectedReservationId}`;
        }
    }));

    // Reservation show page
    Alpine.data('reservationShow', (opts = {}) => ({
        approveConfirmationOpen: false,
        declineConfirmationOpen: false,
        approving: false,
        acceptedOpen: false,
        inventoryWarningOpen: false,
        declineOpen: false,
        overlapWarningOpen: false,
        overlapReservationId: null,
        overlapDate: '',
        insufficientItems: [],
        init() {
            this.acceptedOpen = Boolean(opts?.accepted);
            this.inventoryWarningOpen = Boolean(opts?.inventoryWarning);
            this.insufficientItems = Array.isArray(opts?.insufficientItems) ? opts.insufficientItems : [];
            this.overlapWarningOpen = Boolean(opts?.overlapWarning);
            this.overlapReservationId = opts?.overlapReservationId ?? null;
            this.overlapDate = opts?.overlapDate ?? '';
        },
        openApproveConfirmation() {
            this.approving = false;
            this.declineConfirmationOpen = false;
            this.approveConfirmationOpen = true;
        },
        openDeclineConfirmation() {
            this.approveConfirmationOpen = false;
            this.declineConfirmationOpen = true;
        },
        async handleApprove(event = null) {
            if (this.approving) return;
            const triggerButton = event?.currentTarget || event?.target || null;
            if (window.cmsActionButtons && triggerButton) {
                const started = window.cmsActionButtons.start(triggerButton, triggerButton.dataset.loadingText || 'Approving...');
                if (!started) return;
            }
            this.approving = true;

            const form = document.getElementById('approveForm');
            if (!form) {
                console.warn('Approve form not found');
                this.approving = false;
                if (window.cmsActionButtons && triggerButton) {
                    window.cmsActionButtons.stop(triggerButton);
                }
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let checkUrl = form.dataset.checkUrl || '';
            if (!checkUrl) {
                const action = form.getAttribute('action') || '';
                if (action.includes('/approve')) {
                    checkUrl = action.replace(/\/approve(?:\?.*)?$/, '/check-inventory');
                }
            }

            if (!checkUrl) {
                form.submit();
                return;
            }

            try {
                const res = await fetch(checkUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) {
                    throw new Error(`Inventory check failed: ${res.status}`);
                }

                const data = await res.json();
                if (data && data.sufficient === false) {
                    this.insufficientItems = Array.isArray(data.insufficient_items) ? data.insufficient_items : [];
                    this.approveConfirmationOpen = false;
                    this.inventoryWarningOpen = true;
                    this.approving = false;
                    if (window.cmsActionButtons && triggerButton) {
                        window.cmsActionButtons.stop(triggerButton);
                    }
                    return;
                }

                form.submit();
            } catch (error) {
                console.warn('Inventory check error, submitting anyway', error);
                form.submit();
            }
        },
        proceedWithApproval(event = null) {
            if (this.approving) return;
            const triggerButton = event?.currentTarget || event?.target || null;
            if (window.cmsActionButtons && triggerButton) {
                const started = window.cmsActionButtons.start(triggerButton, triggerButton.dataset.loadingText || 'Approving...');
                if (!started) return;
            }
            this.approving = true;
            const form = document.getElementById('approveForm');
            if (!form) {
                this.approving = false;
                if (window.cmsActionButtons && triggerButton) {
                    window.cmsActionButtons.stop(triggerButton);
                }
                return;
            }
            const forceInput = form.querySelector('#forceApproveInput');
            if (forceInput) forceInput.value = '1';
            form.submit();
        },
        openDeclineForm() {
            this.declineOpen = true;
            this.declineConfirmationOpen = false;
        },
        openDeclineFromOverlap() {
            this.overlapWarningOpen = false;
            this.declineOpen = true;
        }
    }));

    // Messages list page
    Alpine.data('messageList', () => ({
        deleteConfirmationOpen: false,
        selectedMessageId: null,
        openDeleteConfirmation(id) {
            this.selectedMessageId = id ?? null;

            const form = document.getElementById('delete-form');
            const template = form?.dataset?.deleteTemplate || '';
            if (form && template) {
                form.action = template.replace('999999', String(id));
            }

            this.deleteConfirmationOpen = true;
        }
    }));

    // Menu create modal
    Alpine.data('menuCreateModal', (opts = {}) => ({
      isCreateOpen: false,
      isEditOpen: false,
      isDeleteOpen: false,
      createSubmitting: false,
      deleteSubmitting: false,
      currentStep: 1,
      deleteId: null,
      deleteName: '',
      allInventoryItems: opts.inventoryItems || [],
      prices: opts.prices || {
        standard: { breakfast:150, am_snacks:150, lunch:300, pm_snacks:100, dinner:300 },
        special:  { breakfast:170, am_snacks:100, lunch:350, pm_snacks:150, dinner:350 },
      },
      form: {
        type:  opts.defaultType || 'standard',
        meal:  opts.defaultMeal || 'breakfast',
        description: '',
        items: [],
        openDropdowns: new Proxy({}, {
          get(target, prop) { return target[prop] || false; },
          set(target, prop, val) { target[prop] = val; return true; }
        }),
        searchTerms: new Proxy({}, {
          get(target, prop) { return target[prop] || ''; },
          set(target, prop, val) { target[prop] = val; return true; }
        })
      },
      editForm: {
        id: null,
        type: 'standard',
        meal: 'breakfast',
        name: '',
        description: '',
        items: []
      },
      getAllInventoryItems() { return this.allInventoryItems; },
      getIngredientLabel(id) {
        const item = this.allInventoryItems.find(i => i.id == id);
        return item ? item.name : '';
      },
      getIngredientUnit(id) {
        const item = this.allInventoryItems.find(i => i.id == id);
        return item ? (item.unit || '') : '';
      },
      normalizeIngredientId(id) {
        if (id === null || id === undefined || id === '') return null;
        return String(id);
      },
      getDuplicateRecipeIndexes(item) {
        const duplicates = new Set();
        const seen = new Map();
        (item?.recipes || []).forEach((recipe, idx) => {
          const id = this.normalizeIngredientId(recipe?.inventory_item_id);
          if (!id) return;
          if (seen.has(id)) {
            duplicates.add(idx);
            duplicates.add(seen.get(id));
            return;
          }
          seen.set(id, idx);
        });
        return duplicates;
      },
      isRecipeDuplicate(item, rIndex) {
        return this.getDuplicateRecipeIndexes(item).has(rIndex);
      },
      getDuplicateIngredientMessage(item, rIndex) {
        if (!this.isRecipeDuplicate(item, rIndex)) return '';
        const id = item?.recipes?.[rIndex]?.inventory_item_id;
        const label = this.getIngredientLabel(id) || 'Ingredient';
        return `Duplicate ingredient: ${label} already added for this item.`;
      },
      hasDuplicateIngredients(items = null) {
        const list = Array.isArray(items) ? items : this.form.items;
        return (list || []).some(item => this.getDuplicateRecipeIndexes(item).size > 0);
      },
      getAvailableIngredients(item, rIndex, searchTerm = '') {
        const term = (searchTerm || '').toLowerCase();
        const currentId = this.normalizeIngredientId(item?.recipes?.[rIndex]?.inventory_item_id);
        const usedIds = new Set();
        (item?.recipes || []).forEach((recipe, idx) => {
          if (idx === rIndex) return;
          const id = this.normalizeIngredientId(recipe?.inventory_item_id);
          if (id) usedIds.add(id);
        });
        return this.allInventoryItems.filter(inv => {
          const invId = this.normalizeIngredientId(inv?.id);
          if (!invId) return false;
          if (invId !== currentId && usedIds.has(invId)) return false;
          if (!term) return true;
          return (inv?.name || '').toLowerCase().includes(term);
        });
      },
      nextStep() { if (this.canProceed()) this.currentStep++; },
      previousStep() { if (this.currentStep > 1) this.currentStep--; },
      canProceed() {
        if (this.currentStep === 1) return this.form.type && this.form.meal;
        if (this.currentStep === 2) return this.form.items.length > 0 && this.form.items.every(item => item.name && item.name.trim());
        if (this.currentStep === 3) {
          return this.form.items.length > 0 && this.form.items.every(item => {
            if (!item.recipes || item.recipes.length === 0) return false;
            return item.recipes.every(recipe => recipe.inventory_item_id && recipe.quantity_needed && recipe.unit);
          }) && !this.hasDuplicateIngredients();
        }
        return false;
      },
      getValidationErrors() {
        const errors = [];
        this.form.items.forEach((item, itemIndex) => {
          if (!item.name || !item.name.trim()) errors.push(`Item ${itemIndex + 1}: Name is required`);
          if (!item.recipes || item.recipes.length === 0) errors.push(`Item "${item.name || 'Unnamed'}": At least one ingredient is required`);
          else {
            item.recipes.forEach((recipe, recipeIndex) => {
              if (!recipe.inventory_item_id) errors.push(`Item "${item.name}", Ingredient ${recipeIndex + 1}: Ingredient selection is required`);
              if (!recipe.quantity_needed) errors.push(`Item "${item.name}", Ingredient ${recipeIndex + 1}: Quantity is required`);
              if (!recipe.unit) errors.push(`Item "${item.name}", Ingredient ${recipeIndex + 1}: Unit is required`);
            });
            const duplicateIndexes = this.getDuplicateRecipeIndexes(item);
            duplicateIndexes.forEach((dupIndex) => {
              const dupRecipe = item.recipes[dupIndex] || {};
              const ingredientName = this.getIngredientLabel(dupRecipe.inventory_item_id) || 'Ingredient';
              errors.push(`Item "${item.name || 'Unnamed'}", Ingredient ${dupIndex + 1}: Duplicate ingredient (${ingredientName})`);
            });
          }
        });
        return errors;
      },
      submitForm() {
        if (this.createSubmitting) return;
        if (this.currentStep !== 3) {
          alert('Please complete all steps before submitting!');
          return;
        }
        if (!this.form.type || !this.form.meal) {
          alert('Menu type and meal time are required!');
          return;
        }
        if (this.form.items.length === 0) {
          alert('Please add at least one item to the menu!');
          return;
        }
        if (!this.canProceed()) {
          const errors = this.getValidationErrors();
          const errorMessage = 'Please fill in all required fields:\n\n' + errors.join('\n');
          alert(errorMessage);
          return;
        }
        this.submitFormAjax();
      },
      async submitFormAjax() {
        const form = this.$refs.createForm;
        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.createSubmitting = true;
        try {
          const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
          });
          let errorData = null;
          try { errorData = await res.json(); } catch (e) { errorData = { message: await res.text() }; }
          if (!res.ok) {
            const errorMsg = errorData?.message || errorData?.error || 'Unknown error occurred';
            const validationErrors = errorData?.errors ? Object.values(errorData.errors).flat().join('\n') : '';
            alert('Error creating menu:\n' + errorMsg + (validationErrors ? '\n\n' + validationErrors : ''));
            this.createSubmitting = false;
            return;
          }
          this.form = { type: this.form.type, meal: this.form.meal, description: '', items: [], openDropdowns: {}, searchTerms: {} };
          this.currentStep = 1;
          this.isCreateOpen = false;
          window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'menu-create-success', bubbles: true, composed: true }));
          setTimeout(() => {
            window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'menu-create-success', bubbles: true, composed: true }));
            setTimeout(() => location.reload(), 300);
          }, 2000);
        } catch (e) {
          alert('Error creating menu: ' + e.message);
          this.createSubmitting = false;
        }
      },
      openCreate(type = null, meal = null) {
        if (type) this.form.type = type;
        if (meal) this.form.meal = meal;
        this.form.items = [];
        this.form.openDropdowns = {};
        this.form.searchTerms = {};
        this.currentStep = 1;
        this.createSubmitting = false;
        this.isCreateOpen = true;
      },
      close(){ 
        this.isCreateOpen = false; 
        this.currentStep = 1;
        this.createSubmitting = false;
      },
      openEdit(id, name, description, type, meal, items = []) {
        this.editForm.id = id;
        this.editForm.name = name || '';
        this.editForm.description = description || '';
        this.editForm.type = type || 'standard';
        this.editForm.meal = meal || 'breakfast';
        this.editForm.items = (items || []).map(i => ({
          name: i.name,
          type: i.type,
          recipes: (i.recipes || []).map(recipe => ({
            ...recipe,
            unit: recipe.unit || this.getIngredientUnit(recipe.inventory_item_id) || ''
          }))
        }));
        this.$refs.editForm.action = `/admin/menus/${id}`;
        this.isEditOpen = true;
      },
      closeEdit() { this.isEditOpen = false; },
      openDelete(id, name = 'this menu') {
        this.deleteId = id;
        this.deleteName = name || 'this menu';
        this.isDeleteOpen = true;
        document.body.style.overflow = 'hidden';
      },
      closeDelete() {
        this.isDeleteOpen = false;
        this.deleteId = null;
        this.deleteName = '';
        this.deleteSubmitting = false;
        document.body.style.overflow = '';
      },
      async confirmDelete() {
        if (!this.deleteId || this.deleteSubmitting) return;
        this.deleteSubmitting = true;
        const url = `/admin/menus/${this.deleteId}`;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
          const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json, text/plain, */*', 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams({ _method: 'DELETE' })
          });
          if (!res.ok && res.status !== 204) console.warn('Delete failed', await res.text());
          const card = document.getElementById('menu-card-' + this.deleteId);
          if (card) card.remove();
          this.closeDelete();
          window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'menu-delete-success', bubbles: true, composed: true }));
        } catch (e) {
          console.error('Delete error', e);
          this.closeDelete();
        }
      },
      get priceText() {
        const t = this.form.type, m = this.form.meal;
        if (m === 'all') return 'varies by meal';
        const v = (this.prices[t] && this.prices[t][m]) ? this.prices[t][m] : 0;
        return '₱' + Number(v).toFixed(2) + ' / head';
      },
      get editPriceText() {
        const t = this.editForm.type, m = this.editForm.meal;
        if (m === 'all') return 'varies by meal';
        const v = (this.prices[t] && this.prices[t][m]) ? this.prices[t][m] : 0;
        return '₱' + Number(v).toFixed(2) + ' / head';
      }
    }));
});

Alpine.start();
