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
    if (!table) {
        // Seite ohne Tabelle: trotzdem ggf. Confirm-Handler registrieren
    }
    const thead = table.tHead;
    const tbody = table.tBodies[0];
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
        headers.forEach((th, idx) => {
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
            const handler = (e) => {
                e.preventDefault();
                currentDir = currentDir === 'asc' ? 'desc' : 'asc';
                updateIndicator(currentDir);
                doSort(idx, type, currentDir);
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

    // etwas verzögert, damit Bootstrap/DOM fertig ist
    setTimeout(openFirstSearchedCourse, 0);
}

// Auto-Initialisierung nach DOM-Ladung
document.addEventListener('DOMContentLoaded', init);

