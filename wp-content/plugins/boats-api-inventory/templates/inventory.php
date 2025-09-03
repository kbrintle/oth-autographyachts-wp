<?php /* Inventory listing page */ get_header(); ?>
<main class="boats-wrap container-fluid py-5" id="boats-inventory">


    <!-- Mobile-only Filters toggle -->
    <button id="filtersToggle"
            class="filters-toggle mb-3"
            type="button"
            aria-expanded="false"
            aria-controls="filtersPanel">
        <span class="label">Filters</span>
        <span class="count-badge" id="filtersCount">0</span>
    </button>

    <!-- Filters -->
    <div class="card mb-4">
        <div id="filtersPanel" class="card-body filters-panel is-collapsed">

            <!-- Wrap inputs in a real form so JS can track changes easily -->
            <form id="boats-filters" class="boats-filters" novalidate>
                <div class="row g-3 mb-md-3">
                    <!-- Make (custom multiselect) -->
                    <div class="col-12 col-md-3 mb-2">
                        <label class="form-label">Make</label>
                        <div id="make-multiselect" class="position-relative" data-endpoint="/wp-json/boats/v1/facets?fields=make">
                            <div class="form-control d-flex flex-wrap gap-2 align-items-center make-control" role="combobox" aria-expanded="false" tabindex="0">
                                <div class="selected-chips d-flex gap-2 flex-wrap"></div>
                                <input type="search" class="ms-1 flex-grow-1 border-0 p-0 shadow-none make-input" placeholder="Search makes…" aria-label="Search makes" />
                            </div>
                            <div class="dropdown-panel border rounded mt-1 bg-white shadow-sm position-absolute w-100 d-none" style="max-height:240px;overflow:auto;z-index:1000">
                                <ul class="list-unstyled mb-0 makes-ul"></ul>
                            </div>
                            <input type="hidden" id="makeid" name="make">
                        </div>
                    </div>

                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Type</label>
                        <select id="typeid" name="type" class="form-select my-dropdown2"></select>
                    </div>

                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">State</label>
                        <select id="stateid" name="state" class="form-select"></select>
                    </div>

                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Fuel</label>
                        <select id="fueltypeid" name="fuel" class="form-select">
                            <option value="">All</option>
                            <option value="Unleaded">Gas/Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Condition</label>
                        <select id="conditionid" name="condition" class="form-select">
                            <option value="">Any</option>
                            <option value="New">New</option>
                            <option value="Used">Used</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Year Min</label>
                        <input id="yrmin" name="year_min" class="form-control" inputmode="numeric" data-filter="year">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">Year Max</label>
                        <input id="yrmax" name="year_max" class="form-control" inputmode="numeric" data-filter="year">
                    </div>

                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Price Min</label>
                        <input id="prmin" name="price_min" class="form-control" inputmode="numeric" data-filter="price">
                    </div>
                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Price Max</label>
                        <input id="prmax" name="price_max" class="form-control" inputmode="numeric" data-filter="price">
                    </div>

                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Length Min (ft)</label>
                        <input id="lnmin" name="length_min" class="form-control" inputmode="numeric" data-filter="length">
                    </div>
                    <div class="col-6 col-md-2 mb-2">
                        <label class="form-label">Length Max (ft)</label>
                        <input id="lnmax" name="length_max" class="form-control" inputmode="numeric" data-filter="length">
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button id="reset-filters" type="button" class="btn btn-secondary btn-sm mr-1">Reset filters</button>
                    <button id="searchbutton" type="button" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div id="listingholdermain" class="position-relative">
        <div id="inv-loading" class="inv-loading d-none" aria-hidden="true">
            <div class="inv-spinner" role="status" aria-label="Loading"></div>
        </div>

        <ul class="product-list list-unstyled row g-4" id="listingholder"></ul>

        <!-- No-results message -->
        <div id="no-results" class="no-results text-center py-5 d-none">
            <h4 class="mb-2">No Listings Found</h4>
            <p class="mb-3">Try broadening your filters or clearing them.</p>
            <button type="button" id="no-results-reset" class="btn btn-secondary btn-sm">
                Reset Filters
            </button>
        </div>
    </div>
</main>
<?php get_footer(); ?>
