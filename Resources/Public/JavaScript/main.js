var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    if (popoverTriggerEl.className.includes('f3-form-error') === true) {
        return new bootstrap.Popover(popoverTriggerEl)
    } else {
        popoverTriggerEl.removeAttribute('data-bs-toggle');
        popoverTriggerEl.removeAttribute('data-bs-content');
        popoverTriggerEl.removeAttribute('data-bs-placement');
    }
})

// Altersprüfung für Eingaben mit Klasse .hfmCheckAge (Format: YYYY-MM-DD)
document.addEventListener('DOMContentLoaded', function () {
    var ageInputs = document.querySelectorAll('.hfmCheckAge');
    if (!ageInputs || ageInputs.length === 0) return;

    // Hilfsfunktion: Alter berechnen
    function calculateAge(isoDateStr) {
        if (!isoDateStr || typeof isoDateStr !== 'string') return null;
        // Erwartetes Format: YYYY-MM-DD (HTML input type="date")
        var parts = isoDateStr.split('-');
        if (parts.length !== 3) return null;
        var year = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1; // 0-basiert
        var day = parseInt(parts[2], 10);
        if (isNaN(year) || isNaN(month) || isNaN(day)) return null;
        var today = new Date();
        var birthDate = new Date(year, month, day);
        if (isNaN(birthDate.getTime())) return null;
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    // Bootstrap 5 Toast anzeigen (dynamisch erzeugt, falls nicht vorhanden)
    function showAgeToast(titleText, messageText) {
        try {
            // Container sicherstellen
            var container = document.querySelector('.toast-container.joAgeToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container position-fixed w-100 h-100 top-0 start-0 d-flex justify-content-center align-items-center p-3 joAgeToastContainer';
                container.setAttribute('aria-live', 'polite');
                container.setAttribute('aria-atomic', 'true');
                document.body.appendChild(container);
            }

            // Toast-Element erstellen
            var toastEl = document.createElement('div');
            toastEl.className = 'toast align-items-center text-white bg-danger border-0';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            var header = document.createElement('div');
            header.className = 'd-flex';

            var body = document.createElement('div');
            body.className = 'toast-body';
            body.textContent = messageText || 'Ungültiges Alter.';

            var strong = document.createElement('strong');
            strong.className = 'me-auto px-3 py-2';
            strong.textContent = titleText || 'Hinweis';

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn-close btn-close-white me-2 m-auto';
            btn.setAttribute('data-bs-dismiss', 'toast');
            btn.setAttribute('aria-label', 'Close');

            header.appendChild(strong);
            header.appendChild(btn);

            toastEl.appendChild(header);
            toastEl.appendChild(body);

            container.appendChild(toastEl);

            var toast = bootstrap.Toast.getOrCreateInstance(toastEl, {delay: 5000});
            toast.show();

            // Nach Ausblenden aus dem DOM entfernen
            toastEl.addEventListener('hidden.bs.toast', function () {
                if (toastEl && toastEl.parentNode) {
                    toastEl.parentNode.removeChild(toastEl);
                }
            });
        } catch (e) {
            // Fallback: alert
            console && console.warn && console.warn('Toast-Fehler:', e);
            alert((titleText ? titleText + ': ' : '') + (messageText || 'Ungültiges Alter.'));
        }
    }

    function getAgeWarningTexts() {
        var titleNode = document.querySelector('.joAgeWarningTitle');
        var messageNode = document.querySelector('.joAgeWarning');
        return {
            title: titleNode ? titleNode.textContent.trim() : 'Altersprüfung',
            message: messageNode ? messageNode.textContent.trim() : 'Das erlaubte Alter liegt zwischen 17 und 36 Jahren.'
        };
    }

    function validateAndNotify(value) {
        var age = calculateAge(value);
        if (age === null) return; // Kein Toast bei leer/ungültig
        if (age < 17 || age > 36) {
            var texts = getAgeWarningTexts();
            showAgeToast(texts.title, texts.message);
        }
    }

    ageInputs.forEach(function (el) {
        ['blur'].forEach(function (evt) {
            el.addEventListener(evt, function (e) {
                validateAndNotify(e.target.value);
            });
        });
    });
});

// passive teilnehmer events
document.addEventListener('DOMContentLoaded', function () {
    const selectElement = document.getElementById('passivMeldungEvent');
    const collapseElements = [].slice.call(document.querySelectorAll('.hfmPassiveTn'));

    var bsCollapse = collapseElements.map(function (collapseElement) {
        return new bootstrap.Collapse(collapseElement, {toggle: false});
    });

    function checkTnAction() {
        // Check for the specific value
        if (selectElement.value === '0') {
            bsCollapse.map(function (el) {
                el.show();
            });
        } else {
            bsCollapse.map(function (el) {
                el.hide();
            });
        }
    }

    selectElement.addEventListener('change', function () {
        checkTnAction();
    });

    //initial values check
    checkTnAction();
});

// show students passiv mesage
document.addEventListener('DOMContentLoaded', function () {
    const tnactionElement = document.getElementById('passivMeldungEvent');
    const studystatElement = document.getElementById('hfmStudent');
    const collapseElements = [].slice.call(document.querySelectorAll('.hfmPassiveTnStudent'));

    var bsCollapseStudent = collapseElements.map(function (collapseElement) {
        return new bootstrap.Collapse(collapseElement, {toggle: false});
    });

    function checkStudentTnAction() {
        // Check for the specific value
        if (tnactionElement.value === '1' && studystatElement.checked) {
            bsCollapseStudent.map(function (el) {
                console.log('show', el)
                el.show();
            });
        } else {
            bsCollapseStudent.map(function (el) {
                console.log('hide', el)
                el.hide();
            });
        }
    }

    tnactionElement.addEventListener('change', function () {
        checkStudentTnAction();
    });
    studystatElement.addEventListener('change', function () {
        checkStudentTnAction();
    });

    //initial values check
    checkStudentTnAction();
});

// show students passiv mesage
document.addEventListener('DOMContentLoaded', function () {
    const selectElement = document.getElementById('hfmZahlungsArt');
    const zahlartOnlineElement = document.getElementById('zahlartOnline');

    var bsCollapseAdditionalText = new bootstrap.Collapse(zahlartOnlineElement, {toggle: false});

    function checkZahlArtAction() {
        if (selectElement.value === '4') {
            bsCollapseAdditionalText.show();
        } else {
            bsCollapseAdditionalText.hide();

        }
    }

    selectElement.addEventListener('change', function () {
        checkZahlArtAction();
    });

    checkZahlArtAction();


});