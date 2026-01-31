/**
 * Alpine.data() components registered here so they exist after Livewire wire:navigate.
 * Inline scripts in page content do not run when content is replaced, so we register once in the layout.
 */
document.addEventListener('alpine:init', () => {
  const Alpine = window.Alpine;
  if (!Alpine) return;

  Alpine.data('passwordWithRules', (ruleLabels, ruleKeys) => ({
    ruleLabels: ruleLabels || {},
    ruleKeys: ruleKeys || ['min', 'number', 'special', 'uppercase'],
    password: '',
    show: false,
    passed(key) {
      const p = String(this.password || '');
      switch (key) {
        case 'min': return p.length >= 8;
        case 'number': return /[0-9]/.test(p);
        case 'special': return /[^A-Za-z0-9]/.test(p);
        case 'uppercase': return /[A-Z]/.test(p);
        default: return false;
      }
    }
  }));

  Alpine.data('menuCreateModal', (opts = {}) => ({
    isCreateOpen: false,
    isEditOpen: false,
    isDeleteOpen: false,
    currentStep: 1,
    deleteId: null,
    deleteName: '',
    prices: opts.prices || {
      standard: { breakfast: 150, am_snacks: 150, lunch: 300, pm_snacks: 100, dinner: 300 },
      special: { breakfast: 170, am_snacks: 100, lunch: 350, pm_snacks: 150, dinner: 350 },
    },
    form: {
      type: opts.defaultType || 'standard',
      meal: opts.defaultMeal || 'breakfast',
      name: '',
      description: '',
      items: []
    },
    editForm: {
      id: null,
      type: 'standard',
      meal: 'breakfast',
      name: '',
      description: '',
      items: []
    },
    openCreate(type = null, meal = null) {
      if (type) this.form.type = type;
      if (meal) this.form.meal = meal;
      this.form.items = [];
      this.currentStep = 1;
      this.isCreateOpen = true;
    },
    close() {
      this.isCreateOpen = false;
      this.currentStep = 1;
    },
    nextStep() {
      if (this.canProceed()) this.currentStep++;
    },
    previousStep() {
      if (this.currentStep > 1) this.currentStep--;
    },
    canProceed() {
      if (this.currentStep === 1) return this.form.type && this.form.meal;
      if (this.currentStep === 2) return this.form.items.length > 0 && this.form.items.every(item => item.name && item.name.trim());
      if (this.currentStep === 3) return this.form.items.length > 0 && this.form.items.every(item => item.recipes && item.recipes.length > 0);
      return false;
    },
    submitForm() {
      if (this.canProceed() && this.$refs.createForm) this.$refs.createForm.submit();
    },
    openEdit(id, name, description, type, meal, items = []) {
      this.editForm.id = id;
      this.editForm.name = name || '';
      this.editForm.description = description || '';
      this.editForm.type = type || 'standard';
      this.editForm.meal = meal || 'breakfast';
      this.editForm.items = (items || []).map(i => ({ name: i.name, type: i.type, recipes: i.recipes || [] }));
      const form = this.$refs.editForm;
      if (form) form.action = (window.APP_URLS && window.APP_URLS.menusBase ? window.APP_URLS.menusBase : '/admin/menus') + '/' + id;
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
      document.body.style.overflow = '';
    },
    async confirmDelete() {
      if (!this.deleteId) return;
      const base = (window.APP_URLS && window.APP_URLS.menusBase) ? window.APP_URLS.menusBase : '/admin/menus';
      const url = base + '/' + this.deleteId;
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: new URLSearchParams({ _method: 'DELETE' })
        });
        if (!res.ok && res.status !== 204) console.warn('Delete failed', await res.text());
        const card = document.getElementById('menu-card-' + this.deleteId);
        if (card) card.remove();
        this.closeDelete();
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
    },
  }));

  Alpine.data('reservationList', () => ({
    approveConfirmationOpen: false,
    declineConfirmationOpen: false,
    selectedReservationId: null,
    actionType: null,
    openApproveConfirmation(reservationId) {
      this.selectedReservationId = reservationId;
      this.actionType = 'approve';
      this.approveConfirmationOpen = true;
    },
    openDeclineConfirmation(reservationId) {
      this.selectedReservationId = reservationId;
      this.actionType = 'decline';
      this.declineConfirmationOpen = true;
    },
    redirectToShowPage() {
      if (!this.selectedReservationId) return;
      this.approveConfirmationOpen = false;
      this.declineConfirmationOpen = false;
      const template = (window.APP_URLS && window.APP_URLS.reservationsShowTemplate) ? window.APP_URLS.reservationsShowTemplate : '/admin/reservations/__ID__';
      const url = template.replace('__ID__', this.selectedReservationId);
      if (this.actionType === 'decline') window.location.href = url + '#decline';
      else window.location.href = url;
    }
  }));

  Alpine.data('reservationShow', (opts) => ({
    acceptedOpen: false,
    declineOpen: false,
    declineConfirmationOpen: false,
    approveConfirmationOpen: false,
    inventoryWarningOpen: false,
    insufficientItems: [],
    openApproveConfirmation() { this.approveConfirmationOpen = true; },
    openDeclineConfirmation() { this.declineConfirmationOpen = true; },
    openDeclineForm() {
      this.declineConfirmationOpen = false;
      setTimeout(() => { this.declineOpen = true; }, 150);
    },
    handleApprove() {
      this.approveConfirmationOpen = false;
      if (opts.inventoryWarning && opts.insufficientItems && opts.insufficientItems.length > 0) {
        this.insufficientItems = opts.insufficientItems;
        this.inventoryWarningOpen = true;
        return;
      }
      const form = document.getElementById('approveForm');
      if (form) form.submit();
    },
    proceedWithApproval() {
      const input = document.getElementById('forceApproveInput');
      if (input) input.value = '1';
      const form = document.getElementById('approveForm');
      if (form) form.submit();
    },
    init() {
      if (opts.accepted) setTimeout(() => { this.acceptedOpen = true; }, 300);
      if (opts.inventoryWarning && opts.insufficientItems && opts.insufficientItems.length > 0) {
        this.insufficientItems = opts.insufficientItems;
        setTimeout(() => { this.inventoryWarningOpen = true; }, 300);
      }
    }
  }));
});
