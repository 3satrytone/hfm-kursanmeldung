export function init() {
    // Mailing/List.html filtering
    const initMailingFilter = () => {
        console.log('initMailingFilter');
        const searchInput = document.getElementById('searchInput');
        const fbody = document.getElementById('fbody');
        if (!searchInput || !fbody) return;

        const rows = fbody.querySelectorAll('tr');
        const filterRadios = document.querySelectorAll('input[name="filter"]');
        const bezahltRadios = document.querySelectorAll('input[name="bezahlt"]');
        const bezahltagRadios = document.querySelectorAll('input[name="bezahltag"]');
        const statusSelect = document.getElementById('anmeldestatus');
        const dvRadios = document.querySelectorAll('input[name="dv"]');
        const selectAll = document.getElementById('selectAll');

        const updateFilters = () => {
            const searchText = searchInput.value.toLowerCase();
            const filterType = document.querySelector('input[name="filter"]:checked')?.value || '';
            const bezahltVal = document.querySelector('input[name="bezahlt"]:checked')?.value || '';
            const bezahltagVal = document.querySelector('input[name="bezahltag"]:checked')?.value || '';
            const statusVal = statusSelect?.value || '';
            const dvVal = document.querySelector('input[name="dv"]:checked')?.value || '';

            rows.forEach(row => {
                let show = true;

                // Suchwort Filter
                if (searchText) {
                    if (filterType === 'kurs') {
                        const kursText = row.querySelector('.kurs')?.textContent.toLowerCase() || '';
                        if (!kursText.includes(searchText)) show = false;
                    } else if (filterType === 'name') {
                        const nameTexts = Array.from(row.querySelectorAll('.name')).map(n => n.textContent.toLowerCase());
                        if (!nameTexts.some(t => t.includes(searchText))) show = false;
                    } else {
                        const allText = row.querySelector('.all')?.textContent.toLowerCase() || row.textContent.toLowerCase();
                        if (!allText.includes(searchText)) show = false;
                    }
                }

                // ANG bezahlt Filter
                if (show && bezahltVal !== '') {
                    const rowBezahlt = row.querySelector('.bezahlt')?.textContent.trim();
                    if (rowBezahlt !== bezahltVal) show = false;
                }

                // TNG bezahlt Filter
                if (show && bezahltagVal !== '') {
                    const rowBezahltag = row.querySelector('.bezahltag')?.textContent.trim();
                    if (rowBezahltag !== bezahltagVal) show = false;
                }

                // Status Filter
                if (show && statusVal !== '') {
                    // statusVal ist die UID, in der Zeile haben wir status_UID als Klasse oder den Text
                    if (!row.querySelector('.status')?.classList.contains('status_' + statusVal)) show = false;
                }

                // DV fak Filter
                if (show && dvVal !== '') {
                    const rowDv = row.querySelector('.dv')?.textContent.trim();
                    if (rowDv !== dvVal) show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', updateFilters);
        [...filterRadios, ...bezahltRadios, ...bezahltagRadios, ...dvRadios].forEach(r => r.addEventListener('change', updateFilters));
        statusSelect?.addEventListener('change', updateFilters);

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                const checkboxes = fbody.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    if (cb.closest('tr').style.display !== 'none') {
                        cb.checked = selectAll.checked;
                    }
                });
            });
        }
    };

    initMailingFilter();
    
    // Form submission confirmation
    const initFormConfirm = () => {
        const form = document.querySelector('form[name="sendmail"]');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            const confirmMsg = form.dataset.confirm || 'Wirklich senden?';
            if (!window.confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    };

    initFormConfirm();
}

// Auto-Initialisierung nach DOM-Ladung
document.addEventListener('DOMContentLoaded', init);

