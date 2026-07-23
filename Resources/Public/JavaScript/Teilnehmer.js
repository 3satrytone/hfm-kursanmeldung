export function init() {
    // Init Bootstrap collapses (existing behavior)
    const collapseElementList = document.querySelectorAll('.collapse');
    [...collapseElementList].forEach((collapseEl) => {
        try {
            // bootstrap ist im TYPO3-Backend global verfügbar (via dependency 'backend')
            new bootstrap.Collapse(collapseEl, {toggle: false});
        } catch (e) {
            // ignore if bootstrap not present
        }
    });

    // Table sorting for Teilnehmer/List.html
    const table = document.querySelector('table.table');
    const thead = table?.tHead;
    const tbody = table?.tBodies[0];
    if (thead && tbody) {
        // Sortierlogik nur wenn Thead/Body vorhanden sind
    }

    const getCellText = (row, idx) => {
        const cell = row.children[idx];
        return (cell ? cell.textContent : '').trim();
    };

    const parseValue = (text, type) => {
        if (!text) return null;
        switch (type) {
            case 'number': {
                // de-DE number: 1.234,56
                const norm = text.replace(/\./g, '').replace(',', '.');
                const num = parseFloat(norm);
                return isNaN(num) ? text.toLowerCase() : num;
            }
            case 'date':
            case 'datetime': {
                // Expect formats: dd.mm.yyyy or dd.mm.yyyy HH:ii
                const m = text.match(/^(\d{2})\.(\d{2})\.(\d{4})(?:\s+(\d{2})\:(\d{2}))?/);
                if (m) {
                    const [_, d, mo, y, h, mi] = m;
                    const date = new Date(
                        parseInt(y, 10),
                        parseInt(mo, 10) - 1,
                        parseInt(d, 10),
                        h ? parseInt(h, 10) : 0,
                        mi ? parseInt(mi, 10) : 0,
                        0,
                        0
                    );
                    return date.getTime();
                }
                return text.toLowerCase();
            }
            default:
                return text.toLowerCase();
        }
    };

    const doSort = (colIndex, type, direction) => {
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const factor = direction === 'asc' ? 1 : -1;
        rows.sort((a, b) => {
            const va = parseValue(getCellText(a, colIndex), type);
            const vb = parseValue(getCellText(b, colIndex), type);
            if (va === null && vb === null) return 0;
            if (va === null) return -1 * factor;
            if (vb === null) return 1 * factor;
            if (typeof va === 'number' && typeof vb === 'number') {
                return (va - vb) * factor;
            }
            if (va < vb) return -1 * factor;
            if (va > vb) return 1 * factor;
            return 0;
        });
        // Re-append sorted rows
        const frag = document.createDocumentFragment();
        rows.forEach((r) => frag.appendChild(r));
        tbody.appendChild(frag);
    };

    // Add click handlers to sortable headers
    if (thead) {
        const headers = thead.querySelectorAll('th.hdr-srt');
        headers.forEach((th) => {
            th.setAttribute('aria-sort', 'none');
            const indicator = th.querySelector('.srt-ico');
            const updateIndicator = (dir) => {
                headers.forEach((h) => {
                    h.setAttribute('aria-sort', 'none');
                    const i = h.querySelector('.srt-ico');
                    if (i) i.textContent = '';
                });
                th.setAttribute('aria-sort', dir === 'asc' ? 'ascending' : 'descending');
                if (indicator) indicator.textContent = dir === 'asc' ? ' ▲' : ' ▼';
            };

            let currentDir = 'asc';
            const type = th.getAttribute('data-type') || 'text';
            const colIdx = th.cellIndex;
            const handler = (e) => {
                e.preventDefault();
                currentDir = currentDir === 'asc' ? 'desc' : 'asc';
                updateIndicator(currentDir);
                doSort(colIdx, type, currentDir);
            };
            th.addEventListener('click', handler);
            th.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') handler(e);
            });
        });
    }

    // Bootstrap Confirm-Modal für Delete-Links einrichten
    const ensureConfirmModal = () => {
        let modal = document.getElementById('hfmConfirmModal');
        if (modal) return modal;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
<div class="modal fade" id="hfmConfirmModal" tabindex="-1" aria-labelledby="hfmConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="hfmConfirmModalLabel">Löschen bestätigen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
      </div>
      <div class="modal-body">
        <p id="hfmConfirmModalMessage">Möchten Sie diesen Datensatz wirklich löschen?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        <button type="button" class="btn btn-danger" id="hfmConfirmModalOk">Löschen</button>
      </div>
    </div>
  </div>
</div>`;
        const el = wrapper.firstElementChild;
        document.body.appendChild(el);
        return el;
    };

    const attachDeleteConfirm = () => {
        const links = document.querySelectorAll('a.js-confirm-delete');
        if (!links.length) return;

        links.forEach((a) => {
            if (a.dataset.confirmBound === '1') return;
            a.dataset.confirmBound = '1';
            a.addEventListener('click', (e) => {
                const href = a.getAttribute('href');
                if (!href) return;
                try {
                    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                        // Fallback auf native confirm
                        if (window.confirm('Möchten Sie diesen Datensatz wirklich löschen?')) {
                            window.location.href = href;
                        }
                        e.preventDefault();
                        return;
                    }
                    e.preventDefault();
                    const modalEl = ensureConfirmModal();
                    const okBtn = modalEl.querySelector('#hfmConfirmModalOk');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {backdrop: 'static'});

                    // Vorherige Handler entfernen
                    const newOkHandler = () => {
                        modal.hide();
                        window.location.href = href;
                    };
                    // setze einmaligen Handler
                    okBtn.replaceWith(okBtn.cloneNode(true));
                    const okBtnFresh = modalEl.querySelector('#hfmConfirmModalOk');
                    okBtnFresh.addEventListener('click', newOkHandler, {once: true});

                    modal.show();
                } catch (err) {
                    // falls irgendetwas schiefgeht, nativen Confirm verwenden
                    if (window.confirm('Möchten Sie diesen Datensatz wirklich löschen?')) {
                        window.location.href = href;
                    }
                    e.preventDefault();
                }
            });
        });
    };

    attachDeleteConfirm();

    // Status-Select (Anmeldestatus) via AJAX speichern
    const attachStatusHandler = () => {
        const selects = document.querySelectorAll('select.js-ast-select');
        if (!selects.length) return;
        selects.forEach((sel) => {
            if (sel.dataset.astBound === '1') return;
            sel.dataset.astBound = '1';
            sel.addEventListener('change', async () => {
                const url = sel.dataset.url;
                const ka = sel.dataset.ka;
                const ast = sel.value;
                if (!url || !ka || !ast) return;
                // kleines visuelles Feedback
                const oldBg = sel.style.backgroundColor;
                sel.style.backgroundColor = '#fff3cd'; // gelb
                try {
                    const body = new URLSearchParams();
                    body.set('kursanmeldung', String(ka));
                    body.set('anmeldestatus', String(ast));
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: body.toString()
                    });
                    const json = await resp.json().catch(() => null);
                    if (resp.ok && json && json.success) {
                        sel.style.backgroundColor = '#d1e7dd'; // grün
                        setTimeout(() => sel.style.backgroundColor = oldBg || '', 600);
                    } else {
                        sel.style.backgroundColor = '#f8d7da'; // rot
                        setTimeout(() => sel.style.backgroundColor = oldBg || '', 1200);
                    }
                } catch (e) {
                    sel.style.backgroundColor = '#f8d7da';
                    setTimeout(() => sel.style.backgroundColor = oldBg || '', 1200);
                }
            });
        });
    };

    attachStatusHandler();

    // Popup für Feldauswahl an Suchfeldern: öffnen bei Klick/Fokus
    const initFieldPopups = () => {
        const wrappers = document.querySelectorAll('.js-search-wrap');
        if (!wrappers.length) return;

        const closeAll = () => {
            document.querySelectorAll('.js-field-popup.show').forEach((p) => {
                p.classList.remove('show');
                p.style.display = 'none';
            });
        };

        wrappers.forEach((wrap) => {
            const input = wrap.querySelector('.js-search-input');
            const popup = wrap.querySelector('.js-field-popup');
            if (!input || !popup) return;

            let isTriggered = false;

            // Positionierung relativ zum Wrapper
            const openPopup = () => {
                // Wrapper ist position: relative; Popup absolut NEBEN der Inputbox (rechts) anzeigen
                popup.style.position = 'absolute';
                // Vertikal bündig mit der Oberkante des Inputs
                popup.style.top = input.offsetTop + 'px';
                // Horizontal rechts neben dem Input mit kleinem Abstand
                popup.style.left = (input.offsetLeft + input.offsetWidth + 8) + 'px';
                popup.style.display = 'block';
                popup.classList.add('show');
            };

            const closePopup = () => {
                popup.classList.remove('show');
                popup.style.display = 'none';
            };

            if (input.dataset.popupBound === '1') return;
            input.dataset.popupBound = '1';

            input.addEventListener('focus', () => {
                if(!isTriggered) {
                    isTriggered = true;
                    closeAll();
                    openPopup();
                }
            });
            input.addEventListener('click', () => {
                if(!isTriggered) {
                    isTriggered = true;
                    // Toggle bei wiederholtem Klick
                    if (popup.classList.contains('show')) {
                        closePopup();
                    } else {
                        closeAll();
                        openPopup();
                    }
                }
                isTriggered = false;
            });

            // Schließen-Button im Popup
            popup.querySelectorAll('.js-close-popup').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    closePopup();
                });
            });

            // Klick außerhalb schließt Popup
            document.addEventListener('click', (e) => {
                if (!wrap.contains(e.target)) {
                    closePopup();
                }
            });

            // ESC schließt Popup
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closePopup();
                }
            });
        });
    };

    initFieldPopups();

    // Nach Seitenladen: Wenn View das Scroll-Ziel markiert (data-scroll-to="1"),
    // den betroffenen Kurs aufklappen und in den Sichtbereich scrollen
    const openFirstSearchedCourse = () => {
        try {
            const target = document.querySelector('.collapse[data-scroll-to="1"]');
            if (!target) return;

            // Bootstrap Collapse öffnen (falls verfügbar), sonst Klasse setzen
            try {
                const instance = (typeof bootstrap !== 'undefined' && bootstrap.Collapse)
                    ? bootstrap.Collapse.getOrCreateInstance(target, {toggle: false})
                    : null;
                if (instance) {
                    instance.show();
                } else {
                    target.classList.add('show');
                }
            } catch (e) {
                target.classList.add('show');
            }

            // Zum Bereich scrollen (mit Header-Offset)
            const headerOffset = 80; // konfigurierbarer Offset für fixierte Header
            let scrollTarget = target;
            const row = target.closest('.row');
            if (row) scrollTarget = row;
            const rect = scrollTarget.getBoundingClientRect();
            const scrollTop = window.pageYOffset + rect.top - headerOffset;
            window.scrollTo({top: Math.max(scrollTop, 0), behavior: 'smooth'});
        } catch (e) {
            // still
        }
    };

    const availableSelect = document.getElementById('availableFields');
    const selectedList = document.getElementById('selectedFieldsList');
    const moveRightBtn = document.getElementById('moveRight');
    const moveLeftBtn = document.getElementById('moveLeft');
    const exportPreview = document.getElementById('exportPreview');
    const listTabBtn = document.getElementById('list-tab');
    const searchInput = document.getElementById('availableFieldsSearch');

    if (availableSelect && selectedList && moveRightBtn && moveLeftBtn) {
        let pendingMoveOptions = [];

        function moveRight() {
            const selectedOptions = pendingMoveOptions.length > 0 ? pendingMoveOptions : Array.from(availableSelect.options).filter(opt => opt.selected && !opt.disabled);
            pendingMoveOptions = [];
            selectedOptions.forEach(option => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.dataset.value = option.value;
                li.draggable = true;
                li.innerHTML = `<span>${option.text}</span> <button type="button" class="btn-close btn-sm remove-field"></button>`;
                selectedList.appendChild(li);
                option.disabled = true;
                option.style.display = 'none';
                option.selected = false;

                li.querySelector('.remove-field').addEventListener('click', function() {
                    li.remove();
                    option.disabled = false;

                    // Re-apply filter if searching
                    const filter = (searchInput?.value || '').toLowerCase();
                    if (filter === '' || option.text.toLowerCase().includes(filter)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }

                    // Also update optgroup visibility
                    const group = option.closest('optgroup');
                    if (group) {
                        const hasVisible = Array.from(group.querySelectorAll('option')).some(opt => opt.style.display !== 'none');
                        group.style.display = hasVisible ? '' : 'none';
                    }
                });

                li.addEventListener('dragstart', handleDragStart);
                li.addEventListener('dragover', handleDragOver);
                li.addEventListener('dragenter', handleDragEnter);
                li.addEventListener('dragleave', handleDragLeave);
                li.addEventListener('drop', handleDrop);
                li.addEventListener('dragend', handleDragEnd);
            });
        }

        function moveLeft() {
            const items = selectedList.querySelectorAll('.list-group-item');
            items.forEach(li => {
                if (li.classList.contains('active')) {
                    const option = availableSelect.querySelector(`option[value="${li.dataset.value}"]`);
                    if (option) {
                        option.disabled = false;

                        // Re-apply filter if searching
                        const filter = (searchInput?.value || '').toLowerCase();
                        if (filter === '' || option.text.toLowerCase().includes(filter)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }

                        // Also update optgroup visibility
                        const group = option.closest('optgroup');
                        if (group) {
                            const hasVisible = Array.from(group.querySelectorAll('option')).some(opt => opt.style.display !== 'none');
                            group.style.display = hasVisible ? '' : 'none';
                        }
                    }
                    li.remove();
                }
            });
        }

        // Add click-to-select functionality for list items to support moveLeft
        selectedList.addEventListener('click', function(e) {
            const li = e.target.closest('.list-group-item');
            if (li && !e.target.classList.contains('remove-field')) {
                li.classList.toggle('active');
            }
        });

        moveRightBtn.addEventListener('mousedown', function(e) {
            e.preventDefault();
            pendingMoveOptions = Array.from(availableSelect.options).filter(opt => opt.selected && !opt.disabled);
        });
        moveLeftBtn.addEventListener('mousedown', function(e) { e.preventDefault(); });
        moveRightBtn.addEventListener('click', moveRight);
        moveLeftBtn.addEventListener('click', moveLeft);
        availableSelect.addEventListener('dblclick', moveRight);

        // Drag and Drop Logic
        let dragSrcEl = null;

        function handleDragStart(e) {
            this.style.opacity = '0.4';
            dragSrcEl = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
            e.dataTransfer.setData('value', this.dataset.value);
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDragEnter(e) {
            this.classList.add('over');
        }

        function handleDragLeave(e) {
            this.classList.remove('over');
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            this.classList.remove('over');
            if (dragSrcEl !== this) {
                const allItems = Array.from(selectedList.children);
                const dragIdx = allItems.indexOf(dragSrcEl);
                const dropIdx = allItems.indexOf(this);

                if (dragIdx < dropIdx) {
                    this.parentNode.insertBefore(dragSrcEl, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(dragSrcEl, this);
                }
            }
            return false;
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';
        }

        // Exportliste Tab: Filterbare Tabelle laden via AjaxHandler
        const exportlisteTabBtn = document.getElementById('exportliste-tab');
        const exportlisteContent = document.getElementById('exportlisteContent');

        if (exportlisteTabBtn && exportlisteContent) {
            exportlisteTabBtn.addEventListener('show.bs.tab', function () {
                const selectedFields = Array.from(selectedList.children).map(li => li.dataset.value);
                import('@hfm/kursanmeldung/ecma6/AjaxHandler.js').then(function(module) {
                    module.loadExportliste(selectedFields, exportlisteContent);
                });
            });
        }

        // Setup speichern/laden/löschen (mehrere benannte Listen) via AjaxHandler
        const saveSetupBtn = document.getElementById('saveSetup');
        const loadSetupBtn = document.getElementById('loadSetup');
        const deleteSetupBtn = document.getElementById('deleteSetup');
        const setupNameInput = document.getElementById('setupNameInput');
        const savedSetupsSelect = document.getElementById('savedSetupsSelect');

        function applyFieldsToSelectedList(fields) {
            if (!fields || fields.length === 0) return;
            // Zuerst aktuelle Auswahl zurücksetzen
            Array.from(selectedList.children).slice().forEach(function(li) {
                const option = availableSelect.querySelector(`option[value="${li.dataset.value}"]`);
                li.remove();
                if (option) {
                    option.selected = false;
                    option.disabled = false;
                }
            });
            fields.forEach(function(field) {
                const option = availableSelect.querySelector(`option[value="${field}"]`);
                if (option && !option.disabled) {
                    option.selected = true;
                    moveRight();
                }
            });
        }

        function refreshSavedSetupsSelect(selectName) {
            if (!savedSetupsSelect) return;
            import('@hfm/kursanmeldung/ecma6/AjaxHandler.js').then(function(module) {
                module.listSetups().then(function(names) {
                    savedSetupsSelect.innerHTML = '<option value="">-- Liste wählen --</option>';
                    names.forEach(function(name) {
                        const opt = document.createElement('option');
                        opt.value = name;
                        opt.textContent = name;
                        savedSetupsSelect.appendChild(opt);
                    });
                    if (selectName) {
                        savedSetupsSelect.value = selectName;
                    }
                });
            });
        }

        if (saveSetupBtn) {
            saveSetupBtn.addEventListener('click', function() {
                const selectedFields = Array.from(selectedList.children).map(li => li.dataset.value);
                const name = (setupNameInput && setupNameInput.value.trim()) || 'default';
                const oldText = saveSetupBtn.textContent;
                saveSetupBtn.disabled = true;
                import('@hfm/kursanmeldung/ecma6/AjaxHandler.js').then(function(module) {
                    module.saveSetup(name, selectedFields).then(function() {
                        saveSetupBtn.textContent = 'Gespeichert!';
                        refreshSavedSetupsSelect(name);
                        setTimeout(function() {
                            saveSetupBtn.textContent = oldText;
                            saveSetupBtn.disabled = false;
                        }, 1200);
                    }).catch(function() {
                        saveSetupBtn.textContent = 'Fehler!';
                        setTimeout(function() {
                            saveSetupBtn.textContent = oldText;
                            saveSetupBtn.disabled = false;
                        }, 1200);
                    });
                });
            });
        }

        if (loadSetupBtn) {
            loadSetupBtn.addEventListener('click', function() {
                const name = savedSetupsSelect ? savedSetupsSelect.value : '';
                if (!name) return;
                import('@hfm/kursanmeldung/ecma6/AjaxHandler.js').then(function(module) {
                    module.loadSetup(name).then(function(fields) {
                        applyFieldsToSelectedList(fields);
                        if (setupNameInput) {
                            setupNameInput.value = name;
                        }
                    });
                });
            });
        }

        if (savedSetupsSelect) {
            savedSetupsSelect.addEventListener('change', function() {
                const name = savedSetupsSelect.value;
                if (name && setupNameInput) {
                    setupNameInput.value = name;
                }
            });
        }

        if (deleteSetupBtn) {
            deleteSetupBtn.addEventListener('click', function() {
                const name = savedSetupsSelect ? savedSetupsSelect.value : '';
                if (!name) return;
                if (!window.confirm('Liste "' + name + '" wirklich löschen?')) return;
                import('@hfm/kursanmeldung/ecma6/AjaxHandler.js').then(function(module) {
                    module.deleteSetup(name).then(function() {
                        refreshSavedSetupsSelect();
                    });
                });
            });
        }

        // Gespeicherte Listen beim Öffnen des Export-Modals laden, Standard-Setup vorbelegen
        const exportModalEl = document.getElementById('exportModal');
        if (exportModalEl) {
            let setupLoaded = false;
            exportModalEl.addEventListener('show.bs.modal', function() {
                if (setupLoaded) return;
                setupLoaded = true;
                refreshSavedSetupsSelect();
                import('@hfm/kursanmeldung/ecma6/AjaxHandler.js').then(function(module) {
                    module.loadSetup('default').then(function(fields) {
                        applyFieldsToSelectedList(fields);
                    });
                });
            });
        }

        // Preview logic
        if (listTabBtn && exportPreview) {
            listTabBtn.addEventListener('show.bs.tab', function() {
                const selectedFields = Array.from(selectedList.children).map(li => li.dataset.value);
                if (selectedFields.length === 0) {
                    exportPreview.innerHTML = '<div class="alert alert-warning">Bitte wählen Sie mindestens eine Spalte im Setup aus.</div>';
                    return;
                }

                exportPreview.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

                const url = new URL(window.location.origin + window.location.pathname);
                const params = new URLSearchParams(window.location.search);
                params.set('tx_kursanmeldung_kursanmeldung_kursanmeldungteilnehmer[action]', 'exportPreview');
                params.set('tx_kursanmeldung_kursanmeldung_kursanmeldungteilnehmer[fields]', selectedFields.join(','));

                const previewUrl = url.origin + url.pathname + '?' + params.toString();

                fetch(previewUrl)
                    .then(response => response.text())
                    .then(html => {
                        exportPreview.innerHTML = html;
                    })
                    .catch(err => {
                        exportPreview.innerHTML = '<div class="alert alert-danger">Fehler beim Laden der Vorschau.</div>';
                    });
            });
        }

        // Download logic
        const downloadExcelBtn = document.getElementById('downloadExcel');
        const downloadCsvBtn = document.getElementById('downloadCsv');
        if (downloadExcelBtn) {
            downloadExcelBtn.addEventListener('click', function() {
                triggerDownload('xlsx');
            });
        }
        if (downloadCsvBtn) {
            downloadCsvBtn.addEventListener('click', function() {
                triggerDownload('csv');
            });
        }

        function triggerDownload(format) {
            const selectedFields = Array.from(selectedList.children).map(li => li.dataset.value);
            if (selectedFields.length === 0) {
                alert('Bitte wählen Sie mindestens eine Spalte aus.');
                return;
            }

            let baseUrl = downloadExcelBtn?.getAttribute('data-export-url-xlsx');

            if (format === 'csv') {
                baseUrl = downloadCsvBtn?.getAttribute('data-export-url-csv');
            }

            if (!baseUrl) return;

            const url = new URL(baseUrl, window.location.origin);
            url.searchParams.set('fields', selectedFields.join(','));

            // Ausgewählte Zeilen (Checkboxen) als UID-Filter übergeben
            const checkedBoxes = document.querySelectorAll('table.table input.row-select:checked');
            if (checkedBoxes.length > 0) {
                const uids = Array.from(checkedBoxes).map(cb => cb.dataset.uid).filter(Boolean);
                url.searchParams.set('uids', uids.join(','));
            }

            // UIDs der gefilterten Zeilen aus dem Exportliste-Tab übergeben
            if (exportlisteContent) {
                const visibleRows = exportlisteContent.querySelectorAll('#exportlisteTable tbody tr[data-uid]');
                if (visibleRows.length > 0) {
                    const uidsFromTable = Array.from(visibleRows).map(tr => tr.dataset.uid).filter(Boolean);
                    if (uidsFromTable.length > 0) {
                        url.searchParams.set('uids', uidsFromTable.join(','));
                    }
                }
            }

            window.open(url.toString());
        }

        // Alle-auswählen Checkbox
        const selectAllRows = document.getElementById('selectAllRows');
        if (selectAllRows) {
            selectAllRows.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('table.table tbody input.row-select');
                checkboxes.forEach(cb => { cb.checked = selectAllRows.checked; });
            });
            // Sync selectAll wenn einzelne Checkboxen geändert werden
            document.querySelector('table.table tbody')?.addEventListener('change', function(e) {
                if (e.target.classList.contains('row-select')) {
                    const all = document.querySelectorAll('table.table tbody input.row-select');
                    const checked = document.querySelectorAll('table.table tbody input.row-select:checked');
                    selectAllRows.indeterminate = checked.length > 0 && checked.length < all.length;
                    selectAllRows.checked = checked.length === all.length;
                }
            });
        }

        // Search/Filter logic for availableFields
        if (searchInput && availableSelect) {
            searchInput.addEventListener('input', function() {
                const filter = searchInput.value.toLowerCase();
                const optgroups = availableSelect.querySelectorAll('optgroup');

                optgroups.forEach(group => {
                    const options = Array.from(group.querySelectorAll('option'));
                    let groupVisible = false;

                    options.forEach(option => {
                        const isVisibleByFilter = option.text.toLowerCase().includes(filter);
                        if (option.disabled) {
                            option.style.display = 'none';
                            return;
                        }
                        if (isVisibleByFilter) {
                            option.style.display = '';
                            groupVisible = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    if (groupVisible) {
                        group.style.display = '';
                    } else {
                        group.style.display = 'none';
                    }
                });
            });
        }
    }

    // etwas verzögert, damit Bootstrap/DOM fertig ist
    setTimeout(openFirstSearchedCourse, 0);
}

// Auto-Initialisierung: ES-Module laufen nach DOM-Parsen, daher direkt aufrufen
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

