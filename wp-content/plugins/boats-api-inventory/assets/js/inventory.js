/*!
 * Autograph Yachts — Inventory
 * - Make facet (new/legacy), instant-apply
 * - URL sync + REST inventory/filters
 * - Mobile-only Filters toggle + live counter
 * - Shows #no-results block when nothing matches
 */

(function ($) {
    'use strict';
    if (!$) throw new Error('jQuery is required');

    /* ============================ Config ============================ */
    const REST_INVENTORY = (window.BoatsConfig && BoatsConfig.restUrl)    || '/wp-json/boats/v1/inventory';
    const REST_FILTERS   = (window.BoatsConfig && BoatsConfig.filtersUrl)  || '/wp-json/boats/v1/filters';
    const REST_FACETS    = (window.BoatsConfig && BoatsConfig.facetsUrl)   || '/wp-json/boats/v1/facets?fields=make';
    const BASE_URL       = '/yachts-for-sale';

    /* ============================ Helpers ============================ */
    const qs  = (s, c = document) => c.querySelector(s);
    const qsa = (s, c = document) => Array.from(c.querySelectorAll(s));
    const set = (s, v) => { const el = qs(s); if (el) el.value = v ?? ''; };
    const val = (s) => (qs(s) ? qs(s).value : '');
    const txt = (s, v) => { const el = qs(s); if (el) el.textContent = v; };
    const on  = (el, ev, fn) => el && el.addEventListener(ev, fn);
    const num = (s) => { const n = parseFloat(String(s || '').replace(/[^\d.]/g, '')); return Number.isFinite(n) ? n : ''; };
    const debounce = (fn, ms = 250) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    const esc = (s) => String(s || '')
        .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
        .replaceAll('"','&quot;').replaceAll("'",'&#039;');
    const slug = (s) => String(s || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

    const hasSSelect = typeof $.fn.sSelect === 'function';

    function showLoading(on = true) {
        qs('#inv-loading')?.classList.toggle('d-none', !on);
        qs('.spinner')?.classList.toggle('d-none', !on); // small header spinner if present
    }

    // Fallback facet derivation
    const FacetCache = {
        inv: null,
        async get() {
            if (this.inv) return this.inv;
            const res = await fetch(REST_INVENTORY, { credentials: 'same-origin' });
            try { const data = await res.json(); this.inv = Array.isArray(data) ? data : []; }
            catch { this.inv = []; }
            return this.inv;
        }
    };

    /* ============================ Make facet ============================ */
    const USE_NEW   = !!document.getElementById('make-multiselect');
    const ROOT_SEL  = USE_NEW ? '#make-multiselect' : '#mfcname-parent';
    const MENU_SEL  = USE_NEW ? '#make-multiselect .makes-ul' : '#mfcname-parent .custom-dropdown-menu ul';
    const PANEL_SEL = USE_NEW ? '#make-multiselect .dropdown-panel' : '#mfcname-parent .custom-dropdown-menu';

    const makeUI = {
        isNew: USE_NEW,
        root:   $(ROOT_SEL),
        panel:  $(PANEL_SEL),
        menu:   $(MENU_SEL),
        titleBtn: $('#mfcname'), // legacy button text
        hidden: null,            // #makeid (CSV)
        options: [],
        selected: new Set(),
        staging:  new Set(),
        built: false
    };

    // Ensure hidden CSV input exists
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

    function ensureMakeToggleButton() {
        if (!makeUI.isNew) return;
        if (makeUI.root.find('.make-toggle').length) return;
        const ctrl = makeUI.root.find('.make-control');
        const html = '<button type="button" class="btn btn-light w-100 text-start make-toggle">All</button>';
        if (ctrl.length) ctrl.replaceWith(html); else makeUI.root.prepend(html);
    }

    function renderMakeSummary(setToShow = makeUI.selected) {
        const labels  = Array.from(setToShow).map(v => makeUI.options.find(o => o.value === v)?.label || v);
        const summary = labels.length === 0 ? 'All' : (labels.length === 1 ? labels[0] : `${labels.length} selected`);
        if (makeUI.isNew) makeUI.root.find('.make-toggle').text(summary);
        else makeUI.titleBtn.text(summary);
    }

    function syncMakeHidden() { makeUI.hidden.val(Array.from(makeUI.selected).join(',')); }

    function openPanel()  { makeUI.panel.removeClass('d-none'); }
    function closePanel() { makeUI.panel.addClass('d-none'); }

    function buildMakePanel() {
        if (makeUI.built) return;
        makeUI.built = true;
        makeUI.panel.addClass('d-none');

        // Toolbar
        if (!makeUI.panel.find('.facet-toolbar').length) {
            makeUI.panel.prepend(
                `<div class="facet-toolbar p-2 border-bottom bg-white position-sticky top-0">
          <input type="search" class="form-control facet-search make-search" placeholder="Search makes…">
          <div class="d-flex align-items-center gap-2 mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary make-clear">Clear</button>
          </div>
        </div>`
            );
        }

        renderMakeList(makeUI.staging);

        const search = makeUI.panel.find('.make-search')[0];
        if (search) {
            on(search, 'input', debounce(() => filterMakeList(search.value), 90));
            on(search, 'keydown', (e) => { if (e.key === 'Escape') { e.preventDefault(); closePanel(); } });
        }

        makeUI.panel.on('click', '.make-clear', () => {
            makeUI.staging.clear();
            makeUI.menu.find('input[type="checkbox"]').prop('checked', false);
            makeUI.menu.find('.make-all').prop('checked', true);
            makeUI.selected = new Set();
            syncMakeHidden();
            renderMakeSummary();
            updateURLFromUI();
            updateFilterBadge();
            debouncedRunSearch();
        });
    }

    function renderMakeList(targetSet) {
        if (!makeUI.menu.length) return;

        const parts = [];
        parts.push(
            `<li class="px-2 py-1 border-bottom">
        <label class="d-flex align-items-center gap-2">
          <input type="checkbox" class="form-check-input make-all" ${targetSet.size === 0 ? 'checked' : ''}>
          <span>All</span>
        </label>
      </li>`
        );

        makeUI.options.forEach(opt => {
            const checked = targetSet.has(opt.value) ? 'checked' : '';
            const count   = Number.isFinite(opt.count) ? `<span class="ms-auto text-muted small">${opt.count}</span>` : '';
            parts.push(
                `<li class="px-2 py-1 d-flex align-items-center" data-value="${esc(opt.value)}" data-label="${esc(opt.label)}">
          <label class="d-flex align-items-center gap-2 flex-grow-1">
            <input type="checkbox" class="form-check-input make-cb" value="${esc(opt.value)}" ${checked}>
            <span>${esc(opt.label)}</span>
          </label>
          ${count}
        </li>`
            );
        });

        makeUI.menu.html(parts.join(''));
        makeUI.menu.off('change');

        // "All" checkbox
        makeUI.menu.on('change', '.make-all', function () {
            if (this.checked) {
                makeUI.staging.clear();
                makeUI.menu.find('.make-cb').prop('checked', false);
            } else {
                this.checked = (makeUI.staging.size === 0);
            }
            makeUI.selected = new Set(makeUI.staging);
            syncMakeHidden();
            renderMakeSummary();
            updateURLFromUI();
            updateFilterBadge();
            debouncedRunSearch();
        });

        // Individual makes
        makeUI.menu.on('change', '.make-cb', function () {
            const v = this.value;
            if (this.checked) makeUI.staging.add(v); else makeUI.staging.delete(v);
            makeUI.menu.find('.make-all').prop('checked', makeUI.staging.size === 0);
            makeUI.selected = new Set(makeUI.staging);
            syncMakeHidden();
            renderMakeSummary();
            updateURLFromUI();
            updateFilterBadge();
            debouncedRunSearch();
        });
    }

    function filterMakeList(q) {
        const needle = String(q || '').toLowerCase().trim();
        const rows = makeUI.menu.find('li[data-value]');
        if (!needle) { rows.show(); return; }
        rows.each(function () {
            const label = (this.getAttribute('data-label') || '').toLowerCase();
            $(this).toggle(label.includes(needle));
        });
    }

    function initMakeFromURL(params) {
        const csv = params.get('make');
        if (csv) csv.split(',').filter(Boolean).forEach(v => makeUI.selected.add(v));
        makeUI.staging = new Set(makeUI.selected);
        syncMakeHidden();
        renderMakeSummary();
    }

    /* ===================== Populate filters ===================== */
    async function populateFilters({ selectedType = '' } = {}) {
        try {
            const res  = await fetch(REST_FILTERS, { credentials: 'same-origin' });
            const data = await res.json();

            let makes  = (data?.makes || data?.make || []);
            let types  = (data?.types || []);
            let states = (data?.states || data?.state || []);
            let fuels  = (data?.fuels || data?.fuel || []);

            if (!makes.length) {
                try {
                    const r2 = await fetch(REST_FACETS, { credentials: 'same-origin' });
                    const d2 = await r2.json();
                    makes = (d2?.make || d2?.makes || []);
                } catch {}
            }

            if (!makes.length || !types.length || !states.length || !fuels.length) {
                const inv = await FacetCache.get();
                const uniq = (arr) => Array.from(new Set(arr.filter(Boolean)));
                if (!makes.length)  makes  = uniq(inv.map(b => (b.MakeStringExact || b.MakeString || '').trim())).sort();
                if (!types.length)  types  = uniq(inv.map(b => (b.Class || b.BoatType || b.Type || '').toString().trim())).sort();
                if (!states.length) states = uniq(inv.map(b => (b.BoatLocation?.BoatStateCode || b.State || '').toString().trim().toUpperCase())).sort();
                if (!fuels.length)  fuels  = uniq(inv.flatMap(b => {
                    const engs = Array.isArray(b.Engines) ? b.Engines : [];
                    const ef   = engs.map(e => (e.Fuel || '').toString().trim());
                    const top  = (b.Fuel || b.FuelType || '').toString().trim();
                    return [...ef, top];
                })).sort();
            }

            // Normalize Make options
            makeUI.options = makes
                .filter(Boolean)
                .map(m => {
                    if (typeof m === 'string') return { label: m, value: slug(m) };
                    const label = m.label || m.name || String(m);
                    return { label, value: (m.value || slug(label)), count: m.count };
                })
                .filter(o => o.label && o.value)
                .sort((a, b) => (b.count || 0) - (a.count || 0) || a.label.localeCompare(b.label));

            ensureMakeToggleButton();
            buildMakePanel();

            // Type
            const $type = $('#typeid');
            if ($type.length) {
                $type.empty().append('<option value="">All</option>');
                types.forEach(t => $type.append(`<option value="${esc(t)}">${esc(t)}</option>`));
                if (selectedType) $type.val(selectedType);
                if (hasSSelect && $type.hasClass('my-dropdown2')) {
                    $type.sSelect({ ddMaxHeight:'300px', divtextClass:'selectedTxt2', containerClass:'newListSelected2', divwrapperClass:'SSContainerDivWrapper2' });
                    if (selectedType) $('.selectedTxt2').text(selectedType);
                }
            }

            // State
            const $state = $('#stateid');
            if ($state.length) {
                const cur = val('#stateid');
                $state.empty().append('<option value="">All</option>');
                states.forEach(s => $state.append(`<option value="${esc(s)}">${esc(s)}</option>`));
                if (cur) $state.val(cur);
            }

            // Fuel
            const $fuel = $('#fueltypeid');
            if ($fuel.length && fuels.length) {
                const cur = val('#fueltypeid');
                $fuel.empty().append('<option value="">All</option>');
                fuels.forEach(f => $fuel.append(`<option value="${esc(f)}">${esc(f)}</option>`));
                if (cur) $fuel.val(cur);
            }

        } catch { /* non-fatal */ }
    }

    /* ===================== Build query / URL sync ===================== */
    function buildQueryFromUI() {
        const p = new URLSearchParams();
        const makeCsv  = (val('#makeid') || '').trim(); if (makeCsv) p.set('make', makeCsv);
        const type     = val('#typeid');                if (type)     p.set('type', type);
        const state    = val('#stateid');               if (state)    p.set('state', state);
        const condition= val('#conditionid');           if (condition)p.set('condition', condition);
        const fuel     = val('#fueltypeid');            if (fuel)     p.set('fuel', String(fuel).toLowerCase());

        const yrmin = num(val('#yrmin')), yrmax = num(val('#yrmax'));
        if (yrmin || yrmax) p.set('year', `${yrmin || ''}:${yrmax || ''}`);

        const prmin = num(val('#prmin')), prmax = num(val('#prmax'));
        if (prmin || prmax) p.set('price', `${prmin || ''}:${prmax || ''}`);

        const lnmin = num(val('#lnmin')), lnmax = num(val('#lnmax'));
        if (lnmin || lnmax) p.set('length', `${lnmin || ''}:${lnmax || ''}`);

        return p;
    }

    const syncURL = debounce(updateURLFromUI, 200);
    function updateURLFromUI() {
        const p = new URLSearchParams();

        const makeCsv = (val('#makeid') || '').trim(); if (makeCsv) p.set('make', makeCsv);
        const type  = val('#typeid');   if (type)  p.set('type', type);
        const fuel  = val('#fueltypeid'); if (fuel) p.set('fuel', fuel);
        const state = val('#stateid');  if (state) p.set('state', state);

        const lnmin = val('#lnmin'), lnmax = val('#lnmax'); if (lnmin) p.set('minLength', lnmin); if (lnmax) p.set('maxLength', lnmax);
        const prmin = val('#prmin'), prmax = val('#prmax'); if (prmin) p.set('minPrice', prmin);  if (prmax) p.set('maxPrice', prmax);
        const yrmin = val('#yrmin'), yrmax = val('#yrmax'); if (yrmin) p.set('minYear', yrmin);   if (yrmax) p.set('maxYear', yrmax);

        const condition = val('#conditionid'); if (condition) p.set('condition', condition);

        history.replaceState(null, '', `${window.location.pathname}?${p.toString()}`);
    }

    async function queryInventory(params) {
        const res = await fetch(`${REST_INVENTORY}?${params.toString()}`, { credentials: 'same-origin' });
        try { const data = await res.json(); return Array.isArray(data) ? data : []; }
        catch { return []; }
    }

    /* ===================== Render ===================== */
    function renderList(boats) {
        const holder = qs('#listingholder');
        const empty  = qs('#no-results');
        if (!holder) return;

        holder.innerHTML = '';

        if (!Array.isArray(boats) || boats.length === 0) {
            txt('.reccounterupdate', '0');
            empty?.classList.remove('d-none');
            return;
        }

        empty?.classList.add('d-none');
        txt('.reccounterupdate', String(boats.length));

        const html = boats.map(b => {
            const title = [b.ModelYear, b.MakeStringExact || b.MakeString, b.Model].filter(Boolean).join(' ');
            const slugStr = (b.slug || [
                (b.MakeStringExact || b.MakeString || ''),
                (b.Model || ''),
                (b.DocumentID || '')
            ].join('-').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')) || '';
            const href  = `${BASE_URL.replace(/\/$/, '')}/${slugStr}`;
            const img   = (b.Image || '').replace('_XLARGE','_LARGE') || '/wp-content/uploads/default-boat.jpg';
            const price = b.Price ? `$${Number(String(b.Price).replace(/[^\d.]/g,'')).toLocaleString()}` : 'Call';
            const loc   = b.BoatLocation ? [b.BoatLocation.BoatCityName, b.BoatLocation.BoatStateCode].filter(Boolean).join(', ') : '';

            return `
        <li class="col-sm-6 col-md-4 col-lg-3 hidden-listing">
          <div class="product card h-100">
            <div class="thumb position-relative">
              <a href="${href}">
                <img class="card-img-top" src="${img}" alt="${esc(title)}">
              </a>
            </div>
            <div class="meta card-body text-left d-flex flex-column">
              <dl class="card-title"><span title="${esc(title)}">${esc(title)}</span></dl>
              <dl class="card-text">${esc(price)}</dl>
              <dl>${esc(loc)}</dl>
              <dl><a href="${href}" class="w-100 btn btn-outline-secondary btn-sm">View Details</a></dl>
              <div class="clear"></div>
            </div>
          </div>
        </li>`;
        }).join('');

        holder.insertAdjacentHTML('beforeend', html);

        // Nice reveal
        setTimeout(() => {
            qsa('#listingholdermain ul.product-list li').forEach(li => li.classList.remove('hidden-listing'));
        }, 120);
    }

    /* ===================== Search flow ===================== */
    async function runSearch() {
        qsa('#listingholdermain ul.product-list li').forEach(li => li.classList.add('hidden-listing'));
        showLoading(true);
        const data = await queryInventory(buildQueryFromUI());
        renderList(data);
        showLoading(false);
        if (window.innerWidth <= 768) window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    const debouncedRunSearch = debounce(runSearch, 150);

    /* ===================== Filter counter (mobile) ===================== */
    function countSet(aSel, bSel){ return !!(val(aSel) || val(bSel)); }
    function computeFilterCount(){
        let n = 0;
        if ((val('#makeid') || '').trim()) n++;
        ['#typeid','#stateid','#fueltypeid','#conditionid'].forEach(s=>{ if (val(s)) n++; });
        if (countSet('#yrmin','#yrmax')) n++;
        if (countSet('#prmin','#prmax')) n++;
        if (countSet('#lnmin','#lnmax')) n++;
        return n;
    }
    function updateFilterBadge(){
        const c = computeFilterCount();
        const b = qs('#filtersCount'); if (b) b.textContent = String(c);
        const t = qs('#filtersToggle'); if (t) t.setAttribute('aria-label', `Filters (${c})`);
    }
    window.updateFilterBadge = updateFilterBadge;

    /* ===================== Init ===================== */
    $(async function () {
        $('.inventory.fade').removeClass('fade');

        // Mobile filters toggle
        const filtersToggle = qs('#filtersToggle');
        const filtersPanel  = qs('#filtersPanel');
        on(filtersToggle,'click',()=>{
            const expanded = filtersToggle.getAttribute('aria-expanded') === 'true';
            filtersToggle.setAttribute('aria-expanded', String(!expanded));
            filtersPanel.classList.toggle('is-collapsed', expanded);
        });

        // Seed from URL
        const urlParams = new URL(window.location.href).searchParams;
        initMakeFromURL(urlParams);

        // Populate facets
        await populateFilters({ selectedType: urlParams.get('type') || '' });

        // Sync make panel UI
        renderMakeList(makeUI.staging);
        renderMakeSummary();

        // Open/close make panel
        if (makeUI.isNew) {
            makeUI.root.on('click','.make-toggle',()=>{
                makeUI.staging = new Set(makeUI.selected);
                renderMakeList(makeUI.staging);
                renderMakeSummary(makeUI.staging);
                makeUI.panel.toggleClass('d-none');
                const s = makeUI.panel.find('.make-search')[0];
                if (s && !makeUI.panel.hasClass('d-none')) setTimeout(()=>s.focus(),0);
            });
        } else {
            $('#mfcname').on('click',()=>{
                makeUI.staging = new Set(makeUI.selected);
                renderMakeList(makeUI.staging);
                renderMakeSummary(makeUI.staging);
                makeUI.panel.toggleClass('d-none');
                const s = makeUI.panel.find('.make-search')[0];
                if (s && !makeUI.panel.hasClass('d-none')) setTimeout(()=>s.focus(),0);
            });
        }

        // Close on click-outside
        $(document).on('mousedown', (e)=>{ if (makeUI.panel.length && !makeUI.root[0].contains(e.target)) closePanel(); });

        // Apply remaining URL params
        const setIf = (name, sel)=>{ const v=urlParams.get(name); if (v) set(sel,v); };
        setIf('minYear','#yrmin');   setIf('maxYear','#yrmax');
        setIf('minPrice','#prmin');  setIf('maxPrice','#prmax');
        setIf('minLength','#lnmin'); setIf('maxLength','#lnmax');

        const year   = urlParams.get('year');   if (year)   { const [a,b]=year.split(':');  set('#yrmin',a||''); set('#yrmax',b||''); }
        const price  = urlParams.get('price');  if (price)  { const [a,b]=price.split(':'); set('#prmin',a||''); set('#prmax',b||''); }
        const length = urlParams.get('length'); if (length) { const [a,b]=length.split(':');set('#lnmin',a||''); set('#lnmax',b||''); }

        setIf('fuel','#fueltypeid'); setIf('state','#stateid'); setIf('condition','#conditionid');

        // Initial fetch + badge
        await runSearch();
        updateFilterBadge();

        // Search button
        on(qs('#searchbutton'),'click',()=>{ updateFilterBadge(); runSearch(); });

        // Change handlers: sync URL, badge, and search
        qsa('#typeid,#stateid,#fueltypeid,#conditionid,#yrmin,#yrmax,#prmin,#prmax,#lnmin,#lnmax')
            .forEach(el => on(el,'change',()=>{ syncURL(); updateFilterBadge(); debouncedRunSearch(); }));

        // Reset
        $(document).on('click','#reset-filters',function(e){
            e.preventDefault();
            history.replaceState({},'',window.location.pathname);

            makeUI.selected.clear(); makeUI.staging.clear();
            syncMakeHidden(); renderMakeSummary();
            if (makeUI.menu.length) {
                makeUI.menu.find('input[type="checkbox"]').prop('checked',false);
                makeUI.menu.find('.make-all').prop('checked',true);
                const s = makeUI.panel.find('.make-search')[0]; if (s) s.value = '';
                filterMakeList('');
            }

            $('#typeid,#fueltypeid,#stateid,#conditionid').val('');
            $('#yrmin,#yrmax,#prmin,#prmax,#lnmin,#lnmax').val('');
            $('.selectedTxt2').text('All'); // if stylish-select is active

            updateFilterBadge();
            runSearch();
        });

        // (optional) sort placeholders
        $('.sortrecord').on('click',function(){
            $('.sortrecord').removeClass('active asc desc');
            const $t = $(this).addClass('active');
            $t.toggleClass('desc').toggleClass('asc');
        });

        // "No results" reset button
        on(qs('#no-results-reset'), 'click', () => qs('#reset-filters')?.click());
    });
})(window.jQuery);
