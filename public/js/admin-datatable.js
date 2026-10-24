/**
 * ===================================================================
 * VSP TIMES Admin Suite - Universal Auto-DataTable Engine
 * Automatically upgrades HTML tables with >10 rows into interactive
 * DataTables with live search, column sorting, and pagination.
 * ===================================================================
 */

(function () {
    'use strict';

    const I18N = {
        en: {
            show: 'Show',
            entries: 'entries',
            searchPlaceholder: 'Quick filter in table...',
            showing: 'Showing',
            to: 'to',
            of: 'of',
            filtered: '(filtered from {total} total entries)',
            prev: 'Previous',
            next: 'Next',
            noMatchTitle: 'No matching records found',
            noMatchDesc: 'Try adjusting your search terms or clearing the filter.'
        },
        te: {
            show: 'చూపించు',
            entries: 'రికార్డులు',
            searchPlaceholder: 'టేబుల్‌లో శోధించండి...',
            showing: 'మొత్తం {total} రికార్డులలో {start} నుండి {end} చూపిస్తోంది',
            to: 'నుండి',
            of: 'లో',
            filtered: '(మొత్తం {total} నుండి ఫిల్టర్ చేయబడింది)',
            prev: 'మునుపటి',
            next: 'తరువాతి',
            noMatchTitle: 'ఎటువంటి ఫలితాలు లభించలేదు',
            noMatchDesc: 'మీ శోధన పదానికి సరిపోయే రికార్డులు ఏవీ లేవు. దయచేసి వేరొక పదం ప్రయత్నించండి.'
        }
    };

    class AutoDataTable {
        constructor(table, options = {}) {
            this.table = table;
            this.options = Object.assign({
                pageSize: parseInt(table.dataset.pageSize, 10) || 10,
                autoInitLimit: 10,
            }, options);

            this.lang = (document.documentElement.lang === 'te' || document.body.dataset.lang === 'te') ? 'te' : 'en';
            this.t = I18N[this.lang] || I18N.en;

            this.tbody = this.table.querySelector('tbody');
            this.thead = this.table.querySelector('thead');
            if (!this.tbody || !this.thead) return;

            // Cache original rows
            this.allRows = Array.from(this.tbody.querySelectorAll('tr:not(.no-datatable-row):not(.dt-empty-row)'));
            this.filteredRows = [...this.allRows];

            this.currentPage = 1;
            this.pageSize = this.options.pageSize;
            this.sortCol = -1;
            this.sortDir = 'asc';

            this.init();
        }

        init() {
            this.table.dataset.dtInitialized = 'true';

            // Build UI layout
            this.buildContainer();
            this.buildTopBar();
            this.buildBottomBar();
            this.setupSorting();
            this.setupSearch();
            this.setupLengthChange();

            // Hide conflicting static Laravel pagination if present in parent container
            const parent = this.container.parentElement;
            if (parent) {
                const staticPaginations = parent.querySelectorAll('.pagination, nav[role="navigation"]');
                staticPaginations.forEach(p => {
                    if (!p.closest('.dt-container')) {
                        p.style.display = 'none';
                    }
                });
            }

            // Initial render
            this.render();
        }

        buildContainer() {
            // Check if already in container
            let container = this.table.closest('.dt-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'dt-container';
                this.table.parentNode.insertBefore(container, this.table);

                const tableResponsive = document.createElement('div');
                tableResponsive.style.overflowX = 'auto';
                tableResponsive.appendChild(this.table);
                container.appendChild(tableResponsive);
            }
            this.container = container;
        }

        buildTopBar() {
            const topBar = document.createElement('div');
            topBar.className = 'dt-topbar';

            // Length selector
            const lengthDiv = document.createElement('div');
            lengthDiv.className = 'dt-length';
            lengthDiv.innerHTML = `
                <label style="display:flex; align-items:center; gap:8px;">
                    <span>${this.t.show}</span>
                    <select class="dt-length-select">
                        <option value="10" ${this.pageSize === 10 ? 'selected' : ''}>10</option>
                        <option value="25" ${this.pageSize === 25 ? 'selected' : ''}>25</option>
                        <option value="50" ${this.pageSize === 50 ? 'selected' : ''}>50</option>
                        <option value="100" ${this.pageSize === 100 ? 'selected' : ''}>100</option>
                        <option value="-1" ${this.pageSize === -1 ? 'selected' : ''}>${this.lang === 'te' ? 'అన్నీ (All)' : 'All'}</option>
                    </select>
                    <span>${this.t.entries}</span>
                </label>
            `;

            // Instant Search bar
            const searchDiv = document.createElement('div');
            searchDiv.className = 'dt-search-wrapper';
            searchDiv.innerHTML = `
                <i class="fa-solid fa-magnifying-glass dt-search-icon"></i>
                <input type="search" class="dt-search-input" placeholder="${this.t.searchPlaceholder}" aria-label="Search">
                <button type="button" class="dt-search-clear" title="Clear">&times;</button>
            `;

            topBar.appendChild(lengthDiv);
            topBar.appendChild(searchDiv);

            // Prepend topBar above table container
            this.container.insertBefore(topBar, this.container.firstChild);

            this.lengthSelect = topBar.querySelector('.dt-length-select');
            this.searchInput = topBar.querySelector('.dt-search-input');
            this.searchClearBtn = topBar.querySelector('.dt-search-clear');
        }

        buildBottomBar() {
            const bottomBar = document.createElement('div');
            bottomBar.className = 'dt-bottombar';

            const infoDiv = document.createElement('div');
            infoDiv.className = 'dt-info';

            const paginationUl = document.createElement('ul');
            paginationUl.className = 'dt-pagination';

            bottomBar.appendChild(infoDiv);
            bottomBar.appendChild(paginationUl);

            this.container.appendChild(bottomBar);

            this.infoDiv = infoDiv;
            this.paginationUl = paginationUl;
        }

        setupSorting() {
            const headers = Array.from(this.thead.querySelectorAll('th'));
            headers.forEach((th, index) => {
                const text = th.textContent.trim().toLowerCase();
                // Skip action or cover / checkbox columns
                if (th.classList.contains('no-sort') || text.includes('action') || text.includes('చర్య') || text.includes('cover') || text.includes('కవర్') || th.querySelector('input[type="checkbox"]')) {
                    return;
                }

                th.classList.add('dt-sortable');
                const sortIcon = document.createElement('span');
                sortIcon.className = 'dt-sort-icon';
                sortIcon.innerHTML = '<i class="fa-solid fa-sort"></i>';
                th.appendChild(sortIcon);

                th.addEventListener('click', () => {
                    this.sortColumn(index, th);
                });
            });
        }

        sortColumn(index, th) {
            const currentDir = th.classList.contains('dt-sort-asc') ? 'asc' : (th.classList.contains('dt-sort-desc') ? 'desc' : 'none');
            const newDir = currentDir === 'asc' ? 'desc' : 'asc';

            // Reset other headers
            this.thead.querySelectorAll('th.dt-sortable').forEach(h => {
                h.classList.remove('dt-sort-asc', 'dt-sort-desc');
                const icon = h.querySelector('.dt-sort-icon i');
                if (icon) icon.className = 'fa-solid fa-sort';
            });

            th.classList.add(newDir === 'asc' ? 'dt-sort-asc' : 'dt-sort-desc');
            const icon = th.querySelector('.dt-sort-icon i');
            if (icon) icon.className = newDir === 'asc' ? 'fa-solid fa-sort-up' : 'fa-solid fa-sort-down';

            this.sortCol = index;
            this.sortDir = newDir;

            this.filteredRows.sort((a, b) => {
                const cellA = a.children[index]?.innerText?.trim() || '';
                const cellB = b.children[index]?.innerText?.trim() || '';

                // Try number sorting
                const numA = parseFloat(cellA.replace(/[^0-9.-]+/g, ''));
                const numB = parseFloat(cellB.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(numA) && !isNaN(numB) && cellA.match(/\d/) && !cellA.match(/[a-zA-Z]/)) {
                    return newDir === 'asc' ? numA - numB : numB - numA;
                }

                // Try date sorting
                const dateA = Date.parse(cellA);
                const dateB = Date.parse(cellB);
                if (!isNaN(dateA) && !isNaN(dateB) && (cellA.includes('20') || cellA.includes('19') || cellA.includes('-') || cellA.includes('/'))) {
                    return newDir === 'asc' ? dateA - dateB : dateB - dateA;
                }

                // String locale sorting (supports Telugu Unicode)
                return newDir === 'asc' 
                    ? cellA.localeCompare(cellB, undefined, { sensitivity: 'base' })
                    : cellB.localeCompare(cellA, undefined, { sensitivity: 'base' });
            });

            this.currentPage = 1;
            this.render();
        }

        setupSearch() {
            let debounceTimer;
            this.searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.searchInput.value.trim().toLowerCase();
                    this.searchClearBtn.style.display = query ? 'block' : 'none';

                    if (!query) {
                        this.filteredRows = [...this.allRows];
                    } else {
                        this.filteredRows = this.allRows.filter(row => {
                            // Collect text content of row excluding action buttons
                            const cells = Array.from(row.children).filter(td => !td.querySelector('.btn-admin') && !td.querySelector('button'));
                            const text = cells.map(c => c.innerText).join(' ').toLowerCase();
                            return text.includes(query);
                        });
                    }

                    this.currentPage = 1;
                    this.render();
                }, 150);
            });

            this.searchClearBtn.addEventListener('click', () => {
                this.searchInput.value = '';
                this.searchClearBtn.style.display = 'none';
                this.filteredRows = [...this.allRows];
                this.currentPage = 1;
                this.render();
                this.searchInput.focus();
            });
        }

        setupLengthChange() {
            this.lengthSelect.addEventListener('change', () => {
                this.pageSize = parseInt(this.lengthSelect.value, 10);
                this.currentPage = 1;
                this.render();
            });
        }

        render() {
            const total = this.filteredRows.length;
            const pageSize = this.pageSize === -1 ? total : this.pageSize;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));

            if (this.currentPage > totalPages) {
                this.currentPage = totalPages;
            }

            const startIdx = (this.currentPage - 1) * pageSize;
            const endIdx = this.pageSize === -1 ? total : Math.min(startIdx + pageSize, total);

            // Hide all original rows
            this.allRows.forEach(row => {
                row.style.display = 'none';
            });

            // Remove existing empty message if any
            const existingEmpty = this.tbody.querySelector('.dt-empty-row');
            if (existingEmpty) existingEmpty.remove();

            if (total === 0) {
                const colSpan = this.thead.querySelectorAll('th').length || 8;
                const emptyTr = document.createElement('tr');
                emptyTr.className = 'dt-empty-row';
                emptyTr.innerHTML = `
                    <td colspan="${colSpan}" style="padding: 0; border: none;">
                        <div class="dt-no-match-card">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                            <h4>${this.t.noMatchTitle}</h4>
                            <p>${this.t.noMatchDesc}</p>
                        </div>
                    </td>
                `;
                this.tbody.appendChild(emptyTr);
            } else {
                // Display only the sliced page rows
                for (let i = startIdx; i < endIdx; i++) {
                    const row = this.filteredRows[i];
                    if (row) {
                        row.style.display = '';
                        this.tbody.appendChild(row); // Re-order in DOM according to sorting
                    }
                }
            }

            // Update Info label
            this.renderInfo(startIdx, endIdx, total);

            // Update Pagination buttons
            this.renderPagination(totalPages);
        }

        renderInfo(startIdx, endIdx, filteredCount) {
            const totalCount = this.allRows.length;
            const displayStart = filteredCount === 0 ? 0 : startIdx + 1;
            const displayEnd = endIdx;

            if (this.lang === 'te') {
                let info = `మొత్తం <strong>${filteredCount}</strong> రికార్డులలో <strong>${displayStart}</strong> నుండి <strong>${displayEnd}</strong> చూపిస్తోంది`;
                if (filteredCount < totalCount) {
                    info += ` <span style="font-size:12px; color:#94A3B8;">(మొత్తం ${totalCount} రికార్డుల నుండి ఫిల్టర్ చేయబడింది)</span>`;
                }
                this.infoDiv.innerHTML = info;
            } else {
                let info = `Showing <strong>${displayStart}</strong> to <strong>${displayEnd}</strong> of <strong>${filteredCount}</strong> entries`;
                if (filteredCount < totalCount) {
                    info += ` <span style="font-size:12px; color:#94A3B8;">(filtered from ${totalCount} total entries)</span>`;
                }
                this.infoDiv.innerHTML = info;
            }
        }

        renderPagination(totalPages) {
            this.paginationUl.innerHTML = '';
            if (totalPages <= 1) {
                return;
            }

            // Previous Button
            const prevLi = document.createElement('li');
            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'dt-page-btn';
            prevBtn.innerHTML = `<i class="fa-solid fa-chevron-left" style="font-size:11px; margin-right:4px;"></i> ${this.t.prev}`;
            prevBtn.disabled = this.currentPage === 1;
            prevBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.render();
                }
            });
            prevLi.appendChild(prevBtn);
            this.paginationUl.appendChild(prevLi);

            // Numeric Page Buttons
            const range = this.getPageNumbers(this.currentPage, totalPages);
            range.forEach(p => {
                const li = document.createElement('li');
                if (p === '...') {
                    li.innerHTML = `<span class="dt-page-ellipsis">&hellip;</span>`;
                } else {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `dt-page-btn ${p === this.currentPage ? 'active' : ''}`;
                    btn.textContent = p;
                    btn.addEventListener('click', () => {
                        this.currentPage = p;
                        this.render();
                    });
                    li.appendChild(btn);
                }
                this.paginationUl.appendChild(li);
            });

            // Next Button
            const nextLi = document.createElement('li');
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'dt-page-btn';
            nextBtn.innerHTML = `${this.t.next} <i class="fa-solid fa-chevron-right" style="font-size:11px; margin-left:4px;"></i>`;
            nextBtn.disabled = this.currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                if (this.currentPage < totalPages) {
                    this.currentPage++;
                    this.render();
                }
            });
            nextLi.appendChild(nextBtn);
            this.paginationUl.appendChild(nextLi);
        }

        getPageNumbers(current, total) {
            if (total <= 7) {
                return Array.from({ length: total }, (_, i) => i + 1);
            }
            if (current <= 4) {
                return [1, 2, 3, 4, 5, '...', total];
            }
            if (current >= total - 3) {
                return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
            }
            return [1, '...', current - 1, current, current + 1, '...', total];
        }
    }

    // Auto-discovery function: Scans DOM and initializes tables > 10 rows or data-datatable="true"
    function initAutoDataTables() {
        const tables = document.querySelectorAll('table:not([data-dt-initialized="true"])');
        tables.forEach(table => {
            if (table.dataset.datatable === 'false') return;

            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr:not(.no-datatable-row):not(.dt-empty-row)');
            // Condition: more than 10 rows OR explicit data-datatable="true"
            if (rows.length > 10 || table.dataset.datatable === 'true') {
                new AutoDataTable(table);
            }
        });
    }

    // Expose global initializer
    window.AutoDataTable = AutoDataTable;
    window.initAutoDataTables = initAutoDataTables;

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAutoDataTables);
    } else {
        initAutoDataTables();
    }
})();
