/*!
 * Autograph Yachts – Inventory (Facet-style Make filter)
 * - Simple button summary (All / N selected)
 * - Dropdown panel with search + checkbox list
 * - Clear / Apply buttons
 * - URL sync + API calls preserved
 */
(function ($) {
    'use strict';
    if (!$) throw new Error('jQuery is required');
 // return;
    // ============================================================================
    // Config
    // ============================================================================
    const REST_INVENTORY = (window.BoatsConfig && BoatsConfig.restUrl)   || '/wp-json/boats/v1/inventory';
    const REST_FILTERS   = (window.BoatsConfig && BoatsConfig.filtersUrl) || '/wp-json/boats/v1/filters';
    const REST_FACETS    = (window.BoatsConfig && BoatsConfig.facetsUrl)  || '/wp-json/boats/v1/facets?fields=make';
    const BASE_URL       = (window.BoatsConfig && BoatsConfig.baseUrl)    || '/yachts-for-sale';

    // ============================================================================
    // Small helpers
    // ============================================================================
    const qs  = (sel, ctx = document) => ctx.querySelector(sel);
    const qsa = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
    const set = (sel, v) => { const el = qs(sel); if (el) el.value = v ?? ''; };
    const val = (sel) => (qs(sel) ? qs(sel).value : '');
    const text = (sel, v) => { const el = qs(sel); if (el) el.textContent = v; };
    const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
    const cls = (el, name, add) => el && el.classList[add ? 'add' : 'remove'](name);

    const debounce = (fn, ms = 250) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; };
    const num = (s) => { const n = parseFloat(String(s||'').replace(/[^\d.]/g,'')); return Number.isFinite(n) ? n : ''; };
    const slug = (s) => String(s||'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
    const escapeHtml = (s) => String(s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');
    const hasSSelect = typeof $.fn.sSelect === 'function';

    // ---------- facet caching (session) ----------
    const FacetCache = {
        inv: null,                         // unfiltered inventory cache
        async getInventoryUnfiltered() {
            if (FacetCache.inv) return FacetCache.inv;
            const inv = await queryInventory(new URLSearchParams()); // hits cached WP endpoint
            FacetCache.inv = inv;
            return inv;
        }
    };

// ---------- datalist utilities ----------
    function ensureDatalist(inputSel, listId) {
        const input = qs(inputSel);
        if (!input) return null;
        let list = document.getElementById(listId);
        if (!list) {
            list = document.createElement('datalist');
            list.id = listId;
            input.setAttribute('list', listId);
            // place right after the input for clarity
            input.parentNode.appendChild(list);
        }
        return $(list);
    }

    function hydrateDatalist($list, values) {
        if (!$list || !$list.length) return;
        const opts = (values || [])
            .filter(Boolean)
            .map(v => `<option value="${escapeHtml(v)}"></option>`)
            .join('');
        $list.html(opts);
    }

// ---------- facet derivation from inventory ----------
    function deriveFacetsFromInventory(inv) {
        const makes  = new Set();
        const types  = new Set();
        const states = new Set();
        const fuels  = new Set();

        inv.forEach(b => {
            const mk   = (b.MakeStringExact || b.MakeString || '').trim();
            const typ  = (b.Class || b.BoatType || b.Type || '').toString().trim();
            const st   = (b.BoatLocation?.BoatStateCode || b.State || '').toString().trim();


            if (mk)   makes.add(mk);
            if (typ)  types.add(typ);
            if (st)   states.add(st.toUpperCase());
        });

        return {
            makes : Array.from(makes).sort(),
            types : Array.from(types).sort(),
            states: Array.from(states).sort(),
            fuels : Array.from(fuels).sort()
        };
    }

    // ============================================================================
    // MAKE FACET (Facet-style: toggle button + dropdown panel)
    // Supports either markup:
    //  - NEW: #make-multiselect (with .dropdown-panel > .makes-ul)
    //  - OLD: #mfcname-parent (with .custom-dropdown-menu > ul)
    // ============================================================================
    const USE_NEW   = !!document.getElementById('make-multiselect');
    const ROOT_SEL  = USE_NEW ? '#make-multiselect' : '#mfcname-parent';
    const MENU_SEL  = document.querySelector('#make-multiselect .makes-ul')
        ? '#make-multiselect .makes-ul'
        : '#mfcname-parent .custom-dropdown-menu ul';
    const PANEL_SEL = document.querySelector('#make-multiselect .dropdown-panel')
        ? '#make-multiselect .dropdown-panel'
        : '#mfcname-parent .custom-dropdown-menu';

    const makeUI = {
        isNew:    USE_NEW,
        root:     $(ROOT_SEL),
        panel:    $(PANEL_SEL),
        menu:     $(MENU_SEL),
        titleBtn: $('#mfcname'), // old UI visible button; in new UI we render our own
        hidden:   null,          // #makeid CSV
        options:  [],            // [{label, value, count?}]
        selected: new Set(),     // committed selection
        staging:  new Set(),     // pending changes inside panel until Apply
        built:    false
    };

    // Ensure hidden CSV exists
    (function ensureHidden() {
        let hidden = document.getElementById('makeid');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.id = 'makeid';
            makeUI.root.append(hidden);
        }
        makeUI.hidden = $(hidden);
    })();

    // Build simple toggle button for NEW UI (old UI already has #mfcname)
    function ensureMakeToggleButton() {
        if (!makeUI.isNew) return;
        if (makeUI.root.find('.make-toggle').length) return;

        // Replace the "control" area with a single button-like control
        const ctrl = makeUI.root.find('.make-control');
        if (ctrl.length) ctrl.replaceWith('<button type="button" class="btn btn-light w-100 text-start make-toggle">All</button>');
    }

    // Update visible summary text
    function renderMakeSummary(targetSet = makeUI.selected) {
        const labels = Array.from(targetSet).map(v => makeUI.options.find(o => o.value === v)?.label || v);
        const summary =
            labels.length === 0 ? 'All' :
                labels.length === 1 ? labels[0] :
                    `${labels.length} selected`;

        if (makeUI.isNew) {
            makeUI.root.find('.make-toggle').text(summary);
        } else {
            makeUI.titleBtn.text(summary);
        }
        makeUI.root.data('selected-make', labels.join(', ') || 'All');
    }

    function syncMakeHidden() {
        makeUI.hidden.val(Array.from(makeUI.selected).join(','));
    }

    // Render the dropdown panel content (search + list + actions)
    function buildMakePanel() {
        if (makeUI.built) return;
        makeUI.built = true;

        // Ensure panel wrapper exists & is hidden
        makeUI.panel.addClass('d-none');

        // Inject header (search + actions) if not present
        const headerId = 'make-panel-header';
        if (!document.getElementById(headerId)) {
            const header = document.createElement('div');
            header.id = headerId;
            header.className = 'facet-toolbar p-2 border-bottom bg-white position-sticky top-0';
            header.innerHTML = `
    <input type="search" class="form-control facet-search make-search" placeholder="Search makes…">
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-xs make-clear">Clear</button>
<!--      <button type="button" class="btn btn-xs make-apply text-white">Apply</button>-->
    </div>`;
            makeUI.panel.prepend(header);
        }

        // Render list into UL
        renderMakeList(makeUI.staging);

        // Wire actions
        const search = makeUI.panel.find('.make-search')[0];
        on(search, 'input', debounce(() => filterMakeList(search.value), 60));

        search.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); makeUI.panel.find('.make-apply').trigger('click'); }
            if (e.key === 'Escape') { e.preventDefault(); makeUI.panel.addClass('d-none'); }
        });

        makeUI.panel.on('click', '.make-clear', () => {
            makeUI.staging.clear();
            makeUI.menu.find('input[type="checkbox"]').prop('checked', false);
            renderMakeSummary(makeUI.staging); // live preview in button
        });

        makeUI.panel.on('click', '.make-apply', () => {
            makeUI.selected = new Set(Array.from(makeUI.staging)); // commit
            syncMakeHidden();
            renderMakeSummary();
            closePanel();
            updateURLFromUI();
            runSearch(); // trigger search on apply
        });
    }

    function renderMakeList(targetSet) {
        if (!makeUI.menu.length) return;

        // Build items (All + each make)
        const parts = [];
        parts.push(`
      <li class="px-2 py-1 border-bottom">
        <label class="d-flex align-items-center gap-2">
          <input type="checkbox" class="form-check-input make-all" ${targetSet.size === 0 ? 'checked' : ''}>
          <span>All</span>
        </label>
      </li>`);

        makeUI.options.forEach(opt => {
            const checked = targetSet.has(opt.value) ? 'checked' : '';
            const count = (typeof opt.count === 'number') ? `<span class="ms-auto text-muted small">${opt.count}</span>` : '';
            parts.push(`
        <li class="px-2 py-1 d-flex align-items-center" data-value="${opt.value}" data-label="${escapeHtml(opt.label)}">
          <label class="d-flex align-items-center gap-2 flex-grow-1">
            <input type="checkbox" class="form-check-input make-cb" value="${opt.value}" ${checked}>
            <span>${escapeHtml(opt.label)}</span>
          </label>
          ${count}
        </li>`);
        });

        makeUI.menu.html(parts.join(''));

        // Change handlers
        makeUI.menu.off('change');

        // All toggle
        makeUI.menu.on('change', '.make-all', function () {
            if (this.checked) {
                makeUI.staging.clear();
                makeUI.menu.find('.make-cb').prop('checked', false);
            } else {
                this.checked = false; // "All" represents none selected; cannot be unchecked while empty
            }
            renderMakeSummary(makeUI.staging);
        });

        // Individual checkbox changes
        makeUI.menu.on('change', '.make-cb', function () {
            const v = this.value;
            if (this.checked) makeUI.staging.add(v);
            else makeUI.staging.delete(v);

            // update "All" checkbox
            makeUI.menu.find('.make-all').prop('checked', makeUI.staging.size === 0);
            renderMakeSummary(makeUI.staging); // live preview while panel open
        });
    }

    function filterMakeList(query) {
        const q = String(query || '').toLowerCase().trim();
        if (!q) {
            makeUI.menu.find('li[data-value]').show();
            return;
        }
        makeUI.menu.find('li[data-value]').each(function () {
            const label = (this.getAttribute('data-label') || '').toLowerCase();
            $(this).toggle(label.includes(q));
        });
    }

    function openPanel()  { makeUI.panel.removeClass('d-none'); }
    function closePanel() { makeUI.panel.addClass('d-none'); }

    function initMakeFromURL(params) {
        const csv = params.get('make');
        if (csv) csv.split(',').filter(Boolean).forEach(v => makeUI.selected.add(v));
        makeUI.staging = new Set(Array.from(makeUI.selected)); // staging mirrors committed on open
        syncMakeHidden();
        renderMakeSummary();
    }

    // ============================================================================
    // Populate filters (Types + Makes) with safe fallbacks
    // ============================================================================
    // ============================================================================
// Populate filters (Types + Makes + State + Fuel) with safe fallbacks
// ============================================================================
    async function populateFilters({ selectedType = '' } = {}) {
        try {
            // 1) /filters
            const res  = await fetch(REST_FILTERS, { credentials: 'same-origin' });
            const data = await res.json();

            let makes  = (data && (data.makes  || data.make  || [])) || [];
            let types  = (data && (data.types  || [])) || [];
            let states = (data && (data.states || data.state || [])) || [];


            // 2) /facets for makes only (if missing)
            if (!makes.length) {
                try {
                    const r2 = await fetch(REST_FACETS, { credentials: 'same-origin' });
                    const d2 = await r2.json();
                    makes = (d2 && (d2.make || d2.makes || [])) || [];
                } catch (_) {}
            }

            // 3) Derive any missing facets from unfiltered inventory (cached once)
            if (!makes.length || !types.length || !states.length || !fuels.length) {
                try {
                    const inv = await (async () => {
                        if (FacetCache?.inv) return FacetCache.inv;
                        const list = await queryInventory(new URLSearchParams());
                        FacetCache.inv = list;
                        return list;
                    })();

                    const uniq = a => Array.from(new Set(a.filter(Boolean)));
                    if (!makes.length) {
                        makes = uniq(inv.map(b => (b.MakeStringExact || b.MakeString || '').trim())).sort();
                    }
                    if (!types.length) {
                        types = uniq(inv.map(b => (b.Class || b.BoatType || b.Type || '').toString().trim())).sort();
                    }
                    if (!states.length) {
                        states = uniq(inv.map(b => (b.BoatLocation?.BoatStateCode || b.State || '').toString().trim().toUpperCase())).sort();
                    }

                } catch (_) {}
            }

            // --------- MAKES (normalize to [{label,value,count?}]) ---------
            makeUI.options = makes
                .filter(Boolean)
                .map(m => (typeof m === 'string'
                    ? { label: m, value: slug(m) }
                    : { label: m.label || m.name || String(m), value: m.value || slug(m.label || m.name || String(m)), count: m.count }))
                .filter(o => o.label && o.value)
                .sort((a,b) => (b.count||0)-(a.count||0) || a.label.localeCompare(b.label));

            // --------- TYPE <select> ----------
            const $type = $('#typeid');
            if ($type.length) {
                $type.empty().append('<option value="">All</option>');
                types.filter(Boolean).forEach(t => $type.append(`<option value="${escapeHtml(t)}">${escapeHtml(t)}</option>`));
                if (selectedType) $type.val(selectedType);
                if (hasSSelect && $type.hasClass('my-dropdown2')) {
                    $type.sSelect({ ddMaxHeight:'300px', divtextClass:'selectedTxt2', containerClass:'newListSelected2', divwrapperClass:'SSContainerDivWrapper2' });
                    if (selectedType) $('.selectedTxt2').text(selectedType);
                }
            }

            // --------- STATE <select> ----------
            const $state = $('#stateid');
            if ($state.length) {
                const cur = val('#stateid'); // from URL (applied later too)
                $state.empty().append('<option value="">All</option>');
                states.filter(Boolean).forEach(s => $state.append(`<option value="${escapeHtml(s)}">${escapeHtml(s)}</option>`));
                if (cur) $state.val(cur);
                if (hasSSelect && $state.hasClass('my-dropdown2')) $state.sSelect({ ddMaxHeight:'300px' });
            }



            // Build Make facet UI now that options exist
            ensureMakeToggleButton();
            buildMakePanel();
        } catch (_) {
            // noop — page remains usable
        }

    }


    // ============================================================================
    // Query building / URL sync / API
    // ============================================================================
    function buildQueryFromUI() {
        const p = new URLSearchParams();

        const makeCsv = (val('#makeid') || '').trim();
        if (makeCsv) p.set('make', makeCsv);

        const type      = val('#typeid');       if (type)      p.set('type', type);
        const state     = val('#stateid');      if (state)     p.set('state', state);
        const condition = val('#conditionid');  if (condition) p.set('condition', condition);
        const fuel      = val('#fueltypeid');   if (fuel)      p.set('fuel', String(fuel).toLowerCase());

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

        const makeCsv = (val('#makeid') || '').trim();
        if (makeCsv) p.set('make', makeCsv);

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
        let data = [];
        try { data = await res.json(); } catch (_) {}
        return Array.isArray(data) ? data : [];
    }

    // ============================================================================
    // Rendering results
    // ============================================================================
    function renderList(boats) {
        const holder = qs('#listingholder');
        if (!holder) return;
        holder.innerHTML = '';

        if (!boats.length) {
            text('.reccounterupdate', '0');
            cls(qs('.spinner'), 'd-none', true);
            holder.innerHTML = "<h1 class='text-center w-100'>No Results</h1>";
            return;
        }

        text('.reccounterupdate', String(boats.length));
        holder.insertAdjacentHTML('beforeend', boats.map(renderCardHTML).join(''));

        setTimeout(() => {
            cls(qs('.spinner'), 'd-none', true);
            qsa('#listingholdermain ul.product-list li').forEach(li => cls(li, 'hidden-listing', false));
        }, 150);
    }

    function renderCardHTML(b) {
        const title = [b.ModelYear, b.MakeStringExact || b.MakeString, b.Model].filter(Boolean).join(' ');
        const slugStr  = b.slug || [
            (b.MakeStringExact || b.MakeString || '').toString(),
            (b.Model || '').toString(),
            (b.DocumentID || '').toString()
        ].join('-').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');

        const href  = `/yachts-for-sale/${slugStr}`;
        const img   = (b.Image || '').replace('_XLARGE','_LARGE') || '/wp-content/uploads/default-boat.jpg';
        const price = b.Price ? `$${Number(String(b.Price).replace(/[^\d.]/g,'')).toLocaleString()}` : 'Call';
        const loc   = b.BoatLocation ? [b.BoatLocation.BoatCityName, b.BoatLocation.BoatStateCode].filter(Boolean).join(', ') : '';

        return `
      <li class="col-sm-6 col-md-4 col-lg-3 hidden-listing">
        <div class="product card h-100">
          <div class="thumb position-relative">
            <a href="${href}">
              <img class="card-img-top" src="${img}" alt="${escapeHtml(title)}">
            </a>
          </div>
          <div class="meta card-body text-left d-flex flex-column">
            <dl class="card-title"><span title="${escapeHtml(title)}">${escapeHtml(title)}</span></dl>
            <dl class="card-text">${escapeHtml(price)}</dl>
            <dl>${escapeHtml(loc)}</dl>
            <dl><a href="${href}" class="w-100 btn btn-outline-secondary btn-sm">View Details</a></dl>
            <div class="clear"></div>
          </div>
        </div>
      </li>`;
    }

    // ============================================================================
    // Search flow
    // ============================================================================
    async function runSearch() {
        qsa('#listingholdermain ul.product-list li').forEach(li => cls(li, 'hidden-listing', true));
        cls(qs('.spinner'), 'd-none', false);

        const params = buildQueryFromUI();
        const data = await queryInventory(params);
        renderList(data);

        if (window.innerWidth <= 768) window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ============================================================================
    // Init
    // ============================================================================
    $(async function () {
        $('.inventory.fade').removeClass('fade');
        $('h3.ad-search').on('click', () => $('.ad-search-con').slideToggle());

        // Start with panel closed
        makeUI.panel.addClass('d-none');

        const url = new URL(window.location.href);
        const urlParams = url.searchParams;

        // Init makes from URL
        initMakeFromURL(urlParams);

        // Populate filters (includes building Make panel & toggle)
        const selectedType = urlParams.get('type') || '';
        await populateFilters({ selectedType });
        // Ensure list reflects current staging (clone of selected at load)
        renderMakeList(makeUI.staging);
        renderMakeSummary(); // button text

        // Toggle open/close
        if (makeUI.isNew) {
            makeUI.root.on('click', '.make-toggle', function () {
                makeUI.staging = new Set(Array.from(makeUI.selected)); // reset staging on open
                renderMakeList(makeUI.staging);
                renderMakeSummary(makeUI.staging);
                makeUI.panel.toggleClass('d-none');
                // focus search
                const s = makeUI.panel.find('.make-search')[0];
                if (s && !makeUI.panel.hasClass('d-none')) setTimeout(() => s.focus(), 0);
            });
        } else {
            $('#mfcname').on('click', function () {
                makeUI.staging = new Set(Array.from(makeUI.selected));
                renderMakeList(makeUI.staging);
                renderMakeSummary(makeUI.staging);
                makeUI.panel.toggleClass('d-none');
                const s = makeUI.panel.find('.make-search')[0];
                if (s && !makeUI.panel.hasClass('d-none')) setTimeout(() => s.focus(), 0);
            });
        }

        // Click-outside to close
        $(document).on('mousedown', function (e) {
            if (makeUI.panel.length && !makeUI.root[0].contains(e.target)) {
                closePanel();
            }
        });

        // Apply remaining URL params to inputs
        const setIf = (name, sel) => { const v = urlParams.get(name); if (v) set(sel, v); };
        setIf('minYear','#yrmin');  setIf('maxYear','#yrmax');
        setIf('minPrice','#prmin'); setIf('maxPrice','#prmax');
        setIf('minLength','#lnmin');setIf('maxLength','#lnmax');

        const year   = urlParams.get('year');   if (year)   { const [a,b]=year.split(':');  set('#yrmin',a||''); set('#yrmax',b||''); }
        const price  = urlParams.get('price');  if (price)  { const [a,b]=price.split(':'); set('#prmin',a||''); set('#prmax',b||''); }
        const length = urlParams.get('length'); if (length) { const [a,b]=length.split(':');set('#lnmin',a||''); set('#lnmax',b||''); }

        setIf('fuel','#fueltypeid'); setIf('state','#stateid'); setIf('condition','#conditionid');

        // Initial search
        await runSearch();

        // Search button
        on(qs('#searchbutton'), 'click', runSearch);

        // Debounced URL sync
        const syncURL = debounce(updateURLFromUI, 200);
        qsa('#typeid, #lnmin, #lnmax, #stateid, #fueltypeid, #prmin, #prmax, #yrmin, #yrmax, #conditionid')
            .forEach(el => on(el, 'change', syncURL));

        // Reset
        $(document).on('click', '#reset-filters', function (e) {
            e.preventDefault();
            history.replaceState({}, '', window.location.pathname);

            // Clear makes
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

            // Clear others
            $('#typeid').val('');
            $('#fueltypeid').val('');
            $('#lnmin,#lnmax,#prmin,#prmax,#yrmin,#yrmax').val('');
            $('#conditionid').val('');
            $('.selectedTxt2').text('All');

            if (window.updateFilterBadge) { requestAnimationFrame(window.updateFilterBadge); setTimeout(window.updateFilterBadge, 120); }

            runSearch();
        });

        // (Optional) sort placeholder
        $('.sortrecord').on('click', function () {
            $('.sortrecord').removeClass('active asc desc');
            const $t = $(this).addClass('active');
            $t.toggleClass('desc').toggleClass('asc');
        });
    });
})(window.jQuery);
