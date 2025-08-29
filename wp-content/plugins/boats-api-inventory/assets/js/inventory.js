/*!
 * Autograph Yachts — Inventory
 * Facet-style "Make" filter + standard selects
 * - Instant-apply checkboxes (no Apply button needed)
 * - Search-in-panel, Clear, click-outside to close
 * - URL sync + REST inventory/filters
 * - Populates Type, State, Fuel (from /filters or derived)
 */




(function ($) {
    'use strict';
    if (!$) throw new Error('jQuery is required');

    /* ==========================================================================
     * Config
     * ======================================================================= */
    const REST_INVENTORY = (window.BoatsConfig && BoatsConfig.restUrl)    || '/wp-json/boats/v1/inventory';
    const REST_FILTERS   = (window.BoatsConfig && BoatsConfig.filtersUrl)  || '/wp-json/boats/v1/filters';
    const REST_FACETS    = (window.BoatsConfig && BoatsConfig.facetsUrl)   || '/wp-json/boats/v1/facets?fields=make';
    const BASE_URL       = '/yachts-for-sale';

    /* ==========================================================================
     * Small helpers
     * ======================================================================= */
    const qs  = (sel, ctx = document) => ctx.querySelector(sel);
    const qsa = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
    const set = (sel, v) => { const el = qs(sel); if (el) el.value = v ?? ''; };
    const val = (sel) => (qs(sel) ? qs(sel).value : '');
    const text = (sel, v) => { const el = qs(sel); if (el) el.textContent = v; };
    const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
    const cls = (el, name, add) => el && el.classList[add ? 'add' : 'remove'](name);
    const slug = (s) => String(s || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const escapeHtml = (s) => String(s || '')
        .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
        .replaceAll('"','&quot;').replaceAll("'",'&#039;');
    const num = (s) => { const n = parseFloat(String(s || '').replace(/[^\d.]/g,'')); return Number.isFinite(n) ? n : ''; };
    const debounce = (fn, ms = 250) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    const hasSSelect = typeof $.fn.sSelect === 'function';

    // Optional overlay loader support
    function showLoading(on = true) {
        const overlay = qs('#inv-loading');
        if (overlay) overlay.classList.toggle('d-none', !on);
        const headerSpinner = qs('.spinner'); // the small one in header
        if (headerSpinner) headerSpinner.classList.toggle('d-none', !on);
    }

    // Cache unfiltered inventory once (for deriving facets as fallback)
    const FacetCache = {
        inv: null,
        async getInventory() {
            if (this.inv) return this.inv;
            const res = await fetch(REST_INVENTORY, { credentials: 'same-origin' });
            try {
                const data = await res.json();
                this.inv = Array.isArray(data) ? data : [];
            } catch (_) { this.inv = []; }
            return this.inv;
        }
    };

    /* ==========================================================================
     * MAKE FACET (supports NEW #make-multiselect OR legacy #mfcname-parent)
     * ======================================================================= */
    const USE_NEW   = !!document.getElementById('make-multiselect');
    const ROOT_SEL  = USE_NEW ? '#make-multiselect' : '#mfcname-parent';
    const MENU_SEL  = USE_NEW ? '#make-multiselect .makes-ul' : '#mfcname-parent .custom-dropdown-menu ul';
    const PANEL_SEL = USE_NEW ? '#make-multiselect .dropdown-panel' : '#mfcname-parent .custom-dropdown-menu';

    const makeUI = {
        isNew:    USE_NEW,
        root:     $(ROOT_SEL),
        panel:    $(PANEL_SEL),
        menu:     $(MENU_SEL),
        titleBtn: $('#mfcname'),          // only present in legacy UI
        hidden:   null,                   // #makeid (CSV for API)
        options:  [],                     // [{label, value, count?}]
        selected: new Set(),              // committed
        staging:  new Set(),              // used while panel is open (we do instant-apply anyway)
        built:    false
    };

    // Ensure hidden CSV input exists
    (function ensureHiddenCSV() {
        let hidden = document.getElementById('makeid');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.id = 'makeid';
            makeUI.root.append(hidden);
        }
        makeUI.hidden = $(hidden);
    })();

    // For NEW UI: replace .make-control with a single button showing summary
    function ensureMakeToggleButton() {
        if (!makeUI.isNew) return;
        if (makeUI.root.find('.make-toggle').length) return;
        const ctrl = makeUI.root.find('.make-control');
        const html = '<button type="button" class="btn btn-light w-100 text-start make-toggle">All</button>';
        if (ctrl.length) ctrl.replaceWith(html);
        else makeUI.root.prepend(html);
    }

    // Update visible button/label to show "All", "N selected", or the single label
    function renderMakeSummary(setToShow = makeUI.selected) {
        const labels = Array.from(setToShow).map(v => makeUI.options.find(o => o.value === v)?.label || v);
        const summary = labels.length === 0 ? 'All' : (labels.length === 1 ? labels[0] : `${labels.length} selected`);
        if (makeUI.isNew) makeUI.root.find('.make-toggle').text(summary);
        else makeUI.titleBtn.text(summary);
        makeUI.root.data('selected-make', labels.join(', ') || 'All');
    }

    function syncMakeHidden() {
        makeUI.hidden.val(Array.from(makeUI.selected).join(','));
    }

    function openPanel()  { makeUI.panel.removeClass('d-none'); }
    function closePanel() { makeUI.panel.addClass('d-none'); }

    // Build the “Make” panel (search + checkbox list + Clear)
    function buildMakePanel() {
        if (makeUI.built) return;
        makeUI.built = true;

        makeUI.panel.addClass('d-none');

        // Sticky header with search + Clear
        if (!makeUI.panel.find('.facet-toolbar').length) {
            makeUI.panel.prepend(
                '<div class="facet-toolbar p-2 border-bottom bg-white position-sticky top-0">'+
                '<input type="search" class="form-control facet-search make-search" placeholder="Search makes…">'+
                '<div class="d-flex align-items-center gap-2 mt-2">'+
                '<button type="button" class="btn btn-sm btn-outline-secondary make-clear">Clear</button>'+
                '</div>'+
                '</div>'
            );
        }

        // Initial list render
        renderMakeList(makeUI.staging);

        // Search filter
        const search = makeUI.panel.find('.make-search')[0];
        if (search) {
            on(search, 'input', debounce(() => filterMakeList(search.value), 80));
            on(search, 'keydown', (e) => {
                if (e.key === 'Escape') { e.preventDefault(); closePanel(); }
            });
        }

        // Clear -> reset to "All"
        makeUI.panel.on('click', '.make-clear', () => {
            makeUI.staging.clear();
            makeUI.menu.find('input[type="checkbox"]').prop('checked', false);
            makeUI.menu.find('.make-all').prop('checked', true);

            // Instant-apply
            makeUI.selected = new Set(); // empty = All
            syncMakeHidden();
            renderMakeSummary();
            updateURLFromUI();
            debouncedRunSearch();
        });
    }

    // Render checkbox list: “All” + all make options
    function renderMakeList(targetSet) {
        if (!makeUI.menu.length) return;

        const parts = [];
        parts.push(
            '<li class="px-2 py-1 border-bottom">'+
            '<label class="d-flex align-items-center gap-2">'+
            `<input type="checkbox" class="form-check-input make-all" ${targetSet.size === 0 ? 'checked' : ''}>`+
            '<span>All</span>'+
            '</label>'+
            '</li>'
        );

        makeUI.options.forEach(opt => {
            const checked = targetSet.has(opt.value) ? 'checked' : '';
            const count   = Number.isFinite(opt.count) ? `<span class="ms-auto text-muted small">${opt.count}</span>` : '';
            parts.push(
                '<li class="px-2 py-1 d-flex align-items-center" data-value="${opt.value}" data-label="${escapeHtml(opt.label)}">'+
                '<label class="d-flex align-items-center gap-2 flex-grow-1">'+
                '<input type="checkbox" class="form-check-input make-cb" value="${opt.value}" ${checked}>'+
                '<span>${escapeHtml(opt.label)}</span>'+
                '</label>'+
                count+
                '</li>'
            );
        });

        makeUI.menu.html(parts.join(''));

        // (Re)bind change handlers with instant-apply behavior
        makeUI.menu.off('change');

        // Toggle “All”
        makeUI.menu.on('change', '.make-all', function () {
            if (this.checked) {
                makeUI.staging.clear();
                makeUI.menu.find('.make-cb').prop('checked', false);
            } else {
                // "All" represents empty selection; cannot be unchecked with no other picks
                this.checked = (makeUI.staging.size === 0);
            }
            // Commit instantly
            makeUI.selected = new Set(makeUI.staging);
            syncMakeHidden();
            renderMakeSummary();
            updateURLFromUI();
            debouncedRunSearch();
        });

        // Toggle a specific make
        makeUI.menu.on('change', '.make-cb', function () {
            const v = this.value;
            if (this.checked) makeUI.staging.add(v);
            else makeUI.staging.delete(v);

            // Keep "All" in sync with emptiness
            makeUI.menu.find('.make-all').prop('checked', makeUI.staging.size === 0);

            // Commit instantly
            makeUI.selected = new Set(makeUI.staging);
            syncMakeHidden();
            renderMakeSummary();
            updateURLFromUI();
            debouncedRunSearch();
        });
    }

    function filterMakeList(query) {
        const q = String(query || '').toLowerCase().trim();
        const rows = makeUI.menu.find('li[data-value]');
        if (!q) { rows.show(); return; }
        rows.each(function () {
            const label = (this.getAttribute('data-label') || '').toLowerCase();
            $(this).toggle(label.includes(q));
        });
    }

    // Initialize selected makes from URL: ?make=csv
    function initMakeFromURL(params) {
        const csv = params.get('make');
        if (csv) csv.split(',').filter(Boolean).forEach(v => makeUI.selected.add(v));
        makeUI.staging = new Set(makeUI.selected);
        syncMakeHidden();
        renderMakeSummary();
    }

    /* ==========================================================================
     * Populate filters (Make + Type + State + Fuel)
     * ======================================================================= */
    async function populateFilters({ selectedType = '' } = {}) {
        try {
            // 1) Prefer /filters for all facets
            const res  = await fetch(REST_FILTERS, { credentials: 'same-origin' });
            const data = await res.json();

            let makes  = (data && (data.makes  || data.make  || [])) || [];
            let types  = (data && (data.types  || [])) || [];
            let states = (data && (data.states || data.state || [])) || [];
            let fuels  = (data && (data.fuels  || data.fuel  || [])) || [];

            // 2) If makes missing, try /facets
            if (!makes.length) {
                try {
                    const r2 = await fetch(REST_FACETS, { credentials: 'same-origin' });
                    const d2 = await r2.json();
                    makes = (d2 && (d2.make || d2.makes || [])) || [];
                } catch (_) {}
            }

            // 3) Derive any missing facets from the full inventory
            if (!makes.length || !types.length || !states.length || !fuels.length) {
                try {
                    const inv = await FacetCache.getInventory();
                    const uniq = (arr) => Array.from(new Set(arr.filter(Boolean)));

                    if (!makes.length) {
                        makes = uniq(inv.map(b => (b.MakeStringExact || b.MakeString || '').trim())).sort();
                    }
                    if (!types.length) {
                        types = uniq(inv.map(b => (b.Class || b.BoatType || b.Type || '').toString().trim())).sort();
                    }
                    if (!states.length) {
                        states = uniq(inv.map(b => (b.BoatLocation?.BoatStateCode || b.State || '').toString().trim().toUpperCase())).sort();
                    }
                    if (!fuels.length) {
                        fuels = uniq(
                            inv.flatMap(b => {
                                // Try engine fuels then top-level fuel
                                const engs = Array.isArray(b.Engines) ? b.Engines : [];
                                const efs  = engs.map(e => (e.Fuel || '').toString().trim());
                                const top  = (b.Fuel || b.FuelType || '').toString().trim();
                                return [...efs, top];
                            })
                        ).sort();
                    }
                } catch (_) {}
            }

            /* ---- Normalize & build Make facet options ---- */
            makeUI.options = makes
                .filter(Boolean)
                .map(m => {
                    if (typeof m === 'string') return { label: m, value: slug(m) };
                    const label = m.label || m.name || String(m);
                    return { label, value: m.value || slug(label), count: m.count };
                })
                .filter(o => o.label && o.value)
                .sort((a, b) => (b.count || 0) - (a.count || 0) || a.label.localeCompare(b.label));

            ensureMakeToggleButton();
            buildMakePanel();

            /* ---- Populate Type select ---- */
            const $type = $('#typeid');
            if ($type.length) {
                $type.empty().append('<option value="">All</option>');
                types.forEach(t => $type.append(`<option value="${escapeHtml(t)}">${escapeHtml(t)}</option>`));
                if (selectedType) $type.val(selectedType);
                if (hasSSelect && $type.hasClass('my-dropdown2')) {
                    $type.sSelect({ ddMaxHeight:'300px', divtextClass:'selectedTxt2', containerClass:'newListSelected2', divwrapperClass:'SSContainerDivWrapper2' });
                    if (selectedType) $('.selectedTxt2').text(selectedType);
                }
            }

            /* ---- Populate State select ---- */
            const $state = $('#stateid');
            if ($state.length) {
                const cur = val('#stateid');
                $state.empty().append('<option value="">All</option>');
                states.forEach(s => $state.append(`<option value="${escapeHtml(s)}">${escapeHtml(s)}</option>`));
                if (cur) $state.val(cur);
            }

            /* ---- Populate Fuel select (if your HTML uses a static list, this will enhance/merge) ---- */
            const $fuel = $('#fueltypeid');
            if ($fuel.length && fuels.length) {
                // Keep current value if present; ensure "All" first
                const current = val('#fueltypeid');
                $fuel.empty().append('<option value="">All</option>');
                fuels.forEach(f => $fuel.append(`<option value="${escapeHtml(f)}">${escapeHtml(f)}</option>`));
                if (current) $fuel.val(current);
            }

        } catch (_) {
            // Non-fatal; page remains usable
        }
    }

    /* ==========================================================================
     * Build inventory query / URL sync / API
     * ======================================================================= */
    function buildQueryFromUI() {
        const p = new URLSearchParams();

        const makeCsv  = (val('#makeid') || '').trim();    if (makeCsv)  p.set('make', makeCsv);
        const type     = val('#typeid');                   if (type)     p.set('type', type);
        const state    = val('#stateid');                  if (state)    p.set('state', state);
        const condition= val('#conditionid');              if (condition)p.set('condition', condition);

        // Fuel: keep server-side lowercase if needed
        const fuel     = val('#fueltypeid');               if (fuel)     p.set('fuel', String(fuel).toLowerCase());

        // Ranges (colon format)
        const yrmin = num(val('#yrmin')), yrmax = num(val('#yrmax'));
        if (yrmin || yrmax) p.set('year', `${yrmin || ''}:${yrmax || ''}`);

        const prmin = num(val('#prmin')), prmax = num(val('#prmax'));
        if (prmin || prmax) p.set('price', `${prmin || ''}:${prmax || ''}`);

        const lnmin = num(val('#lnmin')), lnmax = num(val('#lnmax'));
        if (lnmin || lnmax) p.set('length', `${lnmin || ''}:${lnmax || ''}`);

        return p;
    }

    function updateURLFromUI() {
        const p = new URLSearchParams();

        const makeCsv = (val('#makeid') || '').trim(); if (makeCsv) p.set('make', makeCsv);
        const type  = val('#typeid');       if (type)  p.set('type', type);
        const fuel  = val('#fueltypeid');   if (fuel)  p.set('fuel', fuel);
        const state = val('#stateid');      if (state) p.set('state', state);

        const lnmin = val('#lnmin'), lnmax = val('#lnmax');
        if (lnmin) p.set('minLength', lnmin);
        if (lnmax) p.set('maxLength', lnmax);

        const prmin = val('#prmin'), prmax = val('#prmax');
        if (prmin) p.set('minPrice', prmin);
        if (prmax) p.set('maxPrice', prmax);

        const yrmin = val('#yrmin'), yrmax = val('#yrmax');
        if (yrmin) p.set('minYear', yrmin);
        if (yrmax) p.set('maxYear', yrmax);

        const condition = val('#conditionid'); if (condition) p.set('condition', condition);

        history.replaceState(null, '', `${window.location.pathname}?${p.toString()}`);
    }

    async function queryInventory(params) {
        const url = `${REST_INVENTORY}?${params.toString()}`;
        const res = await fetch(url, { credentials: 'same-origin' });
        try {
            const data = await res.json();
            return Array.isArray(data) ? data : [];
        } catch (_) { return []; }
    }

    /* ==========================================================================
     * Rendering
     * ======================================================================= */
    function renderList(boats) {
        const holder = qs('#listingholder');
        if (!holder) return;
        holder.innerHTML = '';

        if (!boats.length) {
            text('.reccounterupdate', '0');
            holder.innerHTML = "<h1 class='text-center w-100'>No Results</h1>";
            return;
        }

        text('.reccounterupdate', String(boats.length));

        const items = boats.map(b => {
            const title = [b.ModelYear, b.MakeStringExact || b.MakeString, b.Model].filter(Boolean).join(' ');
            const slugStr = (b.slug || [
                (b.MakeStringExact || b.MakeString || '').toString(),
                (b.Model || '').toString(),
                (b.DocumentID || '').toString()
            ].join('-').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'')) || '';
            const href  = `${BASE_URL.replace(/\/$/, '')}/${slugStr}`;
            const img   = (b.Image || '').replace('_XLARGE','_LARGE') || '/wp-content/uploads/default-boat.jpg';
            const price = b.Price ? `$${Number(String(b.Price).replace(/[^\d.]/g,'')).toLocaleString()}` : 'Call';
            const loc   = b.BoatLocation ? [b.BoatLocation.BoatCityName, b.BoatLocation.BoatStateCode].filter(Boolean).join(', ') : '';

            return (
                '<li class="col-sm-6 col-md-4 col-lg-3 hidden-listing">'+
                '<div class="product card h-100">'+
                '<div class="thumb position-relative">'+
                `<a href="${href}">`+
                `<img class="card-img-top" src="${img}" alt="${escapeHtml(title)}">`+
                '</a>'+
                '</div>'+
                '<div class="meta card-body text-left d-flex flex-column">'+
                `<dl class="card-title"><span title="${escapeHtml(title)}">${escapeHtml(title)}</span></dl>`+
                `<dl class="card-text">${escapeHtml(price)}</dl>`+
                `<dl>${escapeHtml(loc)}</dl>`+
                `<dl><a href="${href}" class="w-100 btn btn-outline-secondary btn-sm">View Details</a></dl>`+
                '<div class="clear"></div>'+
                '</div>'+
                '</div>'+
                '</li>'
            );
        }).join('');

        holder.insertAdjacentHTML('beforeend', items);

        // Reveal with a small delay for a nicer fade-in (if your CSS uses it)
        setTimeout(() => {
            qsa('#listingholdermain ul.product-list li').forEach(li => li.classList.remove('hidden-listing'));
        }, 150);
    }

    /* ==========================================================================
     * Search flow
     * ======================================================================= */
    async function runSearch() {
        // Pre-state
        qsa('#listingholdermain ul.product-list li').forEach(li => li.classList.add('hidden-listing'));
        showLoading(true);

        // Fetch & render
        const params = buildQueryFromUI();
        const data = await queryInventory(params);
        renderList(data);

        showLoading(false);

        // Mobile UX
        if (window.innerWidth <= 768) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    const debouncedRunSearch = debounce(runSearch, 150);

    /* ==========================================================================
     * Init
     * ======================================================================= */
    $(async function () {
        // Remove stray .fade class from older templates
        $('.inventory.fade').removeClass('fade');
        $('h3.ad-search').on('click', () => $('.ad-search-con').slideToggle());

        // Panel starts hidden
        makeUI.panel.addClass('d-none');

        // Seed from URL
        const url = new URL(window.location.href);
        const urlParams = url.searchParams;
        initMakeFromURL(urlParams);

        // Populate facets & selects
        const selectedType = urlParams.get('type') || '';
        await populateFilters({ selectedType });

        // Ensure checkbox list mirrors current selection
        renderMakeList(makeUI.staging);
        renderMakeSummary();

        // Toggle panel open/close
        if (makeUI.isNew) {
            makeUI.root.on('click', '.make-toggle', function () {
                makeUI.staging = new Set(makeUI.selected);     // clone committed
                renderMakeList(makeUI.staging);
                renderMakeSummary(makeUI.staging);
                makeUI.panel.toggleClass('d-none');
                const s = makeUI.panel.find('.make-search')[0];
                if (s && !makeUI.panel.hasClass('d-none')) setTimeout(() => s.focus(), 0);
            });
        } else {
            $('#mfcname').on('click', function () {
                makeUI.staging = new Set(makeUI.selected);
                renderMakeList(makeUI.staging);
                renderMakeSummary(makeUI.staging);
                makeUI.panel.toggleClass('d-none');
                const s = makeUI.panel.find('.make-search')[0];
                if (s && !makeUI.panel.hasClass('d-none')) setTimeout(() => s.focus(), 0);
            });
        }

        // Click-outside to close
        $(document).on('mousedown', function (e) {
            if (makeUI.panel.length && !makeUI.root[0].contains(e.target)) closePanel();
        });

        // Apply remaining URL params to fields
        const setIf = (name, sel) => { const v = urlParams.get(name); if (v) set(sel, v); };
        setIf('minYear',  '#yrmin');  setIf('maxYear',  '#yrmax');
        setIf('minPrice', '#prmin');  setIf('maxPrice', '#prmax');
        setIf('minLength','#lnmin');  setIf('maxLength','#lnmax');

        const year   = urlParams.get('year');   if (year)   { const [a,b]=year.split(':');  set('#yrmin',a||''); set('#yrmax',b||''); }
        const price  = urlParams.get('price');  if (price)  { const [a,b]=price.split(':'); set('#prmin',a||''); set('#prmax',b||''); }
        const length = urlParams.get('length'); if (length) { const [a,b]=length.split(':');set('#lnmin',a||''); set('#lnmax',b||''); }

        setIf('fuel',      '#fueltypeid');
        setIf('state',     '#stateid');
        setIf('condition', '#conditionid');

        // Initial search
        await runSearch();

        // Manual Search button
        on(qs('#searchbutton'), 'click', runSearch);

        // Debounced URL sync when inputs change
        const syncURL = debounce(updateURLFromUI, 200);
        qsa('#typeid, #lnmin, #lnmax, #stateid, #fueltypeid, #prmin, #prmax, #yrmin, #yrmax, #conditionid')
            .forEach(el => on(el, 'change', () => { syncURL(); debouncedRunSearch(); }));

        // Reset filters
        $(document).on('click', '#reset-filters', function (e) {
            e.preventDefault();
            history.replaceState({}, '', window.location.pathname);

            // Reset makes
            makeUI.selected.clear();
            makeUI.staging.clear();
            syncMakeHidden();
            renderMakeSummary();
            if (makeUI.menu.length) {
                makeUI.menu.find('input[type="checkbox"]').prop('checked', false);
                makeUI.menu.find('.make-all').prop('checked', true);
                const s = makeUI.panel.find('.make-search')[0]; if (s) s.value = '';
                filterMakeList('');
            }

            // Reset other fields
            $('#typeid').val('');
            $('#fueltypeid').val('');
            $('#stateid').val('');
            $('#lnmin,#lnmax,#prmin,#prmax,#yrmin,#yrmax').val('');
            $('#conditionid').val('');
            $('.selectedTxt2').text('All'); // if stylish-select is active

            if (window.updateFilterBadge) { requestAnimationFrame(window.updateFilterBadge); setTimeout(window.updateFilterBadge, 120); }

            runSearch();
        });

        // (Optional) sort UI placeholder
        $('.sortrecord').on('click', function () {
            $('.sortrecord').removeClass('active asc desc');
            const $t = $(this).addClass('active');
            $t.toggleClass('desc').toggleClass('asc');
        });
    });
})(window.jQuery);
