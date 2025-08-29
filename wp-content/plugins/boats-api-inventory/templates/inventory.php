<?php /* Inventory listing page */ get_header(); ?>
<main class="boats-wrap container-fluid py-5" id="boats-inventory">
<!--    <h1 class="mb-4">Boat Inventory</h1>-->

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><strong><span class="reccounterupdate">0</span></strong> boats found</div>
        <div class="spinner-border spinner d-none" role="status" aria-hidden="true"></div>
    </div>


    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 mb-md-3">
                <!-- Make (custom dropdown area used by JS) -->
                <div class="col-12 col-md-3  mb-2">
                    <label class="form-label  ">Make</label>

                    <div id="make-multiselect" class="position-relative" data-endpoint="/wp-json/boats/v1/facets?fields=make">
                        <!-- Combobox control -->
                        <div class="form-control d-flex flex-wrap gap-2 align-items-center make-control" role="combobox" aria-expanded="false" tabindex="0">
                            <div class="selected-chips d-flex gap-2 flex-wrap"></div>
                            <input type="search" class="ms-1 flex-grow-1 border-0 p-0 shadow-none make-input" placeholder="Search makes…" aria-label="Search makes" />
                        </div>

                        <!-- Dropdown panel -->
                        <div class="dropdown-panel border rounded mt-1 bg-white shadow-sm position-absolute w-100 d-none" style="max-height:240px;overflow:auto;z-index:1000">
                            <ul class="list-unstyled mb-0 makes-ul"></ul>
                        </div>

                        <!-- Hidden field (CSV) for your search code -->
                        <input type="hidden" id="makeid" value="">
                    </div>
                </div>

                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Type</label>
                    <select id="typeid" class="form-select my-dropdown2">

                    </select>
                </div>

                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">State</label>
                    <select id="stateid" class="form-select"></select>
                </div>

                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Fuel</label>

                    <select id="fueltypeid" class="form-select">
                        <option value="">All</option>
                        <option value="Unleaded">Gas/Petrol</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Electric">Electric</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Condition</label>
                    <select id="conditionid" class="form-select">
                        <option value="">Any</option>
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Year Min</label>
                    <input id="yrmin" class="form-control" inputmode="numeric">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Year Max</label>
                    <input id="yrmax" class="form-control" inputmode="numeric">
                </div>

                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Price Min</label>
                    <input id="prmin" class="form-control" inputmode="numeric">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Price Max</label>
                    <input id="prmax" class="form-control" inputmode="numeric">
                </div>

                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Length Min (ft)</label>
                    <input id="lnmin" class="form-control" inputmode="numeric">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="form-label">Length Max (ft)</label>
                    <input id="lnmax" class="form-control" inputmode="numeric">
                </div>
            </div>

            <div class="mt-3">
                <button id="reset-filters" class="btn btn-secondary btn-sm">Reset filters</button>

                <button id="searchbutton" class="btn btn-primary">Search</button>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div id="listingholdermain" class="position-relative">
        <!-- Inventory loader -->
        <div id="inv-loading" class="inv-loading d-none" aria-hidden="true">
            <div class="inv-spinner" role="status" aria-label="Loading"></div>
        </div>

        <ul class="product-list list-unstyled row g-4" id="listingholder"></ul>
    </div>
</main>
<?php get_footer(); ?>
