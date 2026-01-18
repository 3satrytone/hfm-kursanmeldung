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

// Bestätigungsdialog für Step3-Formular absenden (per Bootstrap-Modal statt native confirm)
document.addEventListener('DOMContentLoaded', function () {
    try {
        var form = document.getElementById('step3form');
        if (!form) return;

        // Mehrfach-Bindungen vermeiden
        if (form.getAttribute('data-confirm-bound') === '1') return;
        form.setAttribute('data-confirm-bound', '1');

        function ensureSubmitConfirmModal() {
            var modal = document.getElementById('step3ConfirmModal');
            if (modal) return modal;
            var wrapper = document.createElement('div');
            wrapper.innerHTML = '\n<div class="modal fade" id="step3ConfirmModal" tabindex="-1" aria-labelledby="step3ConfirmModalLabel" aria-hidden="true">\n  <div class="modal-dialog modal-dialog-centered">\n    <div class="modal-content">\n      <div class="modal-header">\n        <h5 class="modal-title fw-bold" id="step3ConfirmModalLabel">Bestätigung</h5>\n        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>\n      </div>\n      <div class="modal-body">\n        <p id="step3ConfirmModalMessage">Möchten Sie die Anmeldung jetzt verbindlich abschicken?</p>\n      </div>\n      <div class="modal-footer">\n        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="step3ConfirmModalCancel">Abbrechen</button>\n        <button type="button" class="btn btn-primary" id="step3ConfirmModalOk">Ja, abschicken</button>\n      </div>\n    </div>\n  </div>\n</div>';
            var el = wrapper.firstElementChild;
            document.body.appendChild(el);
            return el;
        }

        // Blockierendes Lade-Modal für finale Bestellung (Inhalt aus #popup-orderfinal)
        function showOrderFinalBlockingModal() {
            try {
                var modalEl = document.getElementById('orderFinalModal');
                console.log(modalEl);
                if (!modalEl) return; // wenn nicht vorhanden, einfach überspringen

                // Titel/Text aus #popup-orderfinal übernehmen
                var src = document.getElementById('popup-orderfinal');
                if (src) {
                    var title = src.getAttribute('title') || '';
                    var bodyHtml = src.innerHTML || '';
                    var titleEl = modalEl.querySelector('#orderFinalModalLabel');
                    var bodyEl = modalEl.querySelector('#orderFinalModalContent');
                    if (titleEl) titleEl.textContent = title;
                    if (bodyEl) bodyEl.innerHTML = bodyHtml;
                }

                var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {backdrop: 'static', keyboard: false});
                modal.show();
            } catch (ex) {
                // Ignoriere Fehler, Modal ist rein kosmetisch
                if (window && window.console && console.warn) {
                    console.warn('OrderFinalModal konnte nicht angezeigt werden:', ex);
                }
            }
        }

        // Listener im Capture-Phase registrieren, damit vor anderen Submit-Handlern bestätigt wird
        form.addEventListener('submit', function (e) {
            // Wenn bereits bestätigt wurde (z. B. bei programmatic submit), nicht erneut fragen
            if (form.getAttribute('data-confirmed') === '1') return;

            // Nachricht aus data-Attribut, ansonsten Fallback-Text
            var msg = form.getAttribute('data-confirm');
            if (!msg) {
                var node = document.querySelector('.step3-confirm-message');
                msg = node && node.textContent ? node.textContent.trim() : 'Möchten Sie die Anmeldung jetzt verbindlich abschicken?';
            }

            var titleNode = document.querySelector('.step3-confirm-title');
            var title = titleNode && titleNode.textContent ? titleNode.textContent.trim() : 'Bestätigung';

            var btnCancelNode = document.querySelector('.step3-confirm-btn-cancel');
            var btnCancel = btnCancelNode && btnCancelNode.textContent ? btnCancelNode.textContent.trim() : 'Abbrechen';

            var btnSubmitNode = document.querySelector('.step3-confirm-btn-submit');
            var btnSubmit = btnSubmitNode && btnSubmitNode.textContent ? btnSubmitNode.textContent.trim() : 'Ja, abschicken';

            // Wenn Bootstrap nicht vorhanden ist, auf native confirm zurückfallen
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                if (!window.confirm(msg)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                form.setAttribute('data-confirmed', '1');
                return true;
            }

            // Mit Modal bestätigen
            e.preventDefault();
            e.stopPropagation();

            var modalEl = ensureSubmitConfirmModal();
            var msgEl = modalEl.querySelector('#step3ConfirmModalMessage');
            if (msgEl) msgEl.textContent = msg;

            var titleEl = modalEl.querySelector('#step3ConfirmModalLabel');
            if (titleEl) titleEl.textContent = title;

            var btnOkEl = modalEl.querySelector('#step3ConfirmModalOk');
            if (btnOkEl) btnOkEl.textContent = btnSubmit;

            var btnCancelEl = modalEl.querySelector('#step3ConfirmModalCancel');
            if (btnCancelEl) btnCancelEl.textContent = btnCancel;


            var okBtn = modalEl.querySelector('#step3ConfirmModalOk');
            // vorherige Handler entfernen, indem wir Knoten klonen
            okBtn.replaceWith(okBtn.cloneNode(true));
            var okBtnFresh = modalEl.querySelector('#step3ConfirmModalOk');

            var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {backdrop: 'static'});
            okBtnFresh.addEventListener('click', function () {
                form.setAttribute('data-confirmed', '1');
                modal.hide();
                // Blockierendes Lade-Popup anzeigen
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    showOrderFinalBlockingModal();
                }
                // programmatic submit, damit andere Submit-Handler normal laufen
                form.submit();
            }, {once: true});

            modal.show();
            return false;
        }, true);
    } catch (err) {
        // Falls etwas schiefgeht, keine Blockade des Submits verursachen
        if (window && window.console && console.warn) {
            console.warn('Step3 Confirm-Init fehlgeschlagen:', err);
        }
    }
});

// show students passiv mesage
document.addEventListener('DOMContentLoaded', function () {
    //toggle passiv students @see collapsePassivMeldung()
    const passivMeldungEl = document.getElementById('passivMeldungEvent');
    const studyStatEl = document.getElementById('hfmStudent');
    const hfmPassiveTnStudentEl = [].slice.call(document.querySelectorAll('.hfmPassiveTnStudent'));
    const submitButtonsEl = [].slice.call(document.querySelectorAll('.formhandler-button[type="submit"]'));

    //toggle matrikel field if studystat
    const matrikelCollapseEl = document.getElementById('collapseMatrikel');

    //show additional message for zahlungsart 4 @see toggleMessageZahlartOnline()
    const selectElementZahlungsArt = document.getElementById('hfmZahlungsArt');
    const zahlartOnlineElement = document.getElementById('zahlartOnline');

    //toggle program if aktiv or passiv selected @see togglePassivAktivSelection()
    const hfmPassiveTnEl = [].slice.call(document.querySelectorAll('.hfmPassiveTn'));

    //toggle hotel prices see toggleHotelRoom()
    const hotelSelectorEl = document.getElementById('hotelSelector');
    const roomSelectorEl = document.getElementById('roomSelector');
    const roomWithEl = document.getElementById('roomWith');
    const hotelCollapseEl = [].slice.call(document.querySelectorAll('.collapseRoom'));
    const hotelFeeCollapseEl = [].slice.call(document.querySelectorAll('.hotelfee-price'));

    //toggle duo selection
    const duoSelectorEl = document.getElementById('duoSelector');
    const duoCollapseEl = [].slice.call(document.querySelectorAll('.duo-collapse'));

    function isAccessible(variable) {
        let propper;

        propper = variable !== undefined;
        if (variable === null) {
            propper = false;
        }

        return propper;
    }

    function initBSCollapse(el) {
        return el.map(function (collapseElement) {
            return new bootstrap.Collapse(collapseElement, {toggle: false});
        });
    }

    function hideEl(src) {
        src.map(function (el) {
            el.hide();
        });
    }

    function showEl(src) {
        src.map(function (el) {
            el.show();
        });
    }

    function collapsePassivMeldung() {
        if (!isAccessible(passivMeldungEl) || !isAccessible(studyStatEl)) {
            return;
        }
        let bsCollapseSubmitButton = initBSCollapse(submitButtonsEl);
        let bsCollapseStudent = initBSCollapse(hfmPassiveTnStudentEl);

        function checkStudentTnAction() {
            // Check for the specific value
            if (passivMeldungEl.value === '1' && studyStatEl.checked) {
                showEl(bsCollapseStudent);
                hideEl(bsCollapseSubmitButton);
            } else {
                hideEl(bsCollapseStudent);
                showEl(bsCollapseSubmitButton);
            }
        }

        passivMeldungEl.addEventListener('change', function () {
            checkStudentTnAction();
        });
        studyStatEl.addEventListener('change', function () {
            checkStudentTnAction();
        });

        //initial values check
        checkStudentTnAction();
    }

    function toggleMatrikel(){
        if (!isAccessible(matrikelCollapseEl) || !isAccessible(studyStatEl)) {
            return;
        }
        let bsCollapseMatrikelEl = new bootstrap.Collapse(matrikelCollapseEl, {toggle: false});

        function checkMatrikelAction(){
            if(studyStatEl.checked){
                bsCollapseMatrikelEl.show();
            }else{
                bsCollapseMatrikelEl.hide();
            }
        }

        studyStatEl.addEventListener('change', function () {
            checkMatrikelAction();
        });

        checkMatrikelAction();
    }

    function toggleMessageZahlartOnline() {
        if (!isAccessible(selectElementZahlungsArt)) {
            return;
        }
        let bsCollapseAdditionalText = new bootstrap.Collapse(zahlartOnlineElement, {toggle: false});

        function checkZahlArtAction() {
            if (selectElementZahlungsArt.value === '4') {
                bsCollapseAdditionalText.show();
            } else {
                bsCollapseAdditionalText.hide();
            }
        }

        selectElementZahlungsArt.addEventListener('change', function () {
            checkZahlArtAction();
        });

        checkZahlArtAction();
    }

    function togglePassivAktivSelection() {
        if (!isAccessible(passivMeldungEl)) {
            return;
        }
        let bsCollapse = initBSCollapse(hfmPassiveTnEl);

        function checkTnAction() {
            // Check for the specific value
            if (passivMeldungEl.value === '0') {
                showEl(bsCollapse);
            } else {
                hideEl(bsCollapse);
            }
        }

        passivMeldungEl.addEventListener('change', function () {
            checkTnAction();
        });

        //initial values check
        checkTnAction();
    }

    function toggleHotelRoom() {
        if (!isAccessible(hotelSelectorEl) || !isAccessible(hotelCollapseEl)) {
            return;
        }
        let bsCollapseHotelEl = initBSCollapse(hotelCollapseEl);
        let bsCollapseRoomWithEl = new bootstrap.Collapse(roomWithEl, {toggle: false});

        function checkHotelRoomAction() {
            if (hotelSelectorEl.value === '') {
                hideEl(bsCollapseHotelEl);
            } else {
                showEl(bsCollapseHotelEl);
            }
            checkRoomWith();
        }

        function checkRoomWith() {
            if (isAccessible(roomSelectorEl) && isAccessible(roomWithEl)) {
                if (roomSelectorEl.value === 'dz2preis') {
                    bsCollapseRoomWithEl.show();
                } else {
                    bsCollapseRoomWithEl.hide();
                }
            }
        }

        function showCurrentPrice() {
            console.log(isAccessible(hotelSelectorEl), !isAccessible(roomSelectorEl))
            if (!isAccessible(hotelSelectorEl) || !isAccessible(roomSelectorEl)) {
                return;
            }
            var fee = document.getElementById('hotelfee-' + hotelSelectorEl.value + '-' + roomSelectorEl.value)

            if (isAccessible(fee)) {
                fee.classList.add('show');
            }
        }

        function checkRoomPrice() {
            if (!isAccessible(hotelFeeCollapseEl)) {
                return;
            }

            hotelFeeCollapseEl.map(function (el) {
                el.classList.remove('show')
            });

            showCurrentPrice();
        }

        hotelSelectorEl.addEventListener('change', function () {
            checkHotelRoomAction();
            checkRoomPrice();
        });

        if (isAccessible(roomSelectorEl)) {
            roomSelectorEl.addEventListener('change', function () {
                checkRoomWith();
                checkRoomPrice();
            });
        }

        checkHotelRoomAction();
        checkRoomPrice();
    }

    function toggleDuoSelection() {
        if (!isAccessible(duoSelectorEl)) {
            return;
        }
        let bsCollapseDuo = initBSCollapse(duoCollapseEl);

        function checkDuoAction() {
            // Check for the specific value
            if (duoSelectorEl.checked) {
                showEl(bsCollapseDuo);
            } else {
                hideEl(bsCollapseDuo);
            }
        }

        duoSelectorEl.addEventListener('change', function () {
            checkDuoAction();
        });

        //initial values check
        checkDuoAction();
    }

    toggleMatrikel();
    collapsePassivMeldung();
    toggleMessageZahlartOnline();
    togglePassivAktivSelection();
    toggleHotelRoom();
    toggleDuoSelection();

    // Dynamically add new upload fields up to the limit in hidden #maxUploadItem
    function checkUploadMaxItem() {
        var maxEl = document.getElementById('maxUploadItem');
        if (!maxEl) {
            return;
        }
        var max = parseInt(maxEl.value, 10);
        if (isNaN(max) || max < 1) {
            return;
        }

        var initialInputs = [].slice.call(document.querySelectorAll('input.upload-field[type="file"]'));
        if (!initialInputs.length) {
            return;
        }

        function getParent(el) {
            // use nearest wrapper if available, else parentElement
            var wrapper = el.closest && el.closest('.upload-field-group');
            return wrapper || el.parentElement || document.body;
        }

        function ensureSlots(parent) {
            var inputs = [].slice.call(parent.querySelectorAll('input.upload-field[type="file"]'));
            if (!inputs.length) {
                return;
            }

            // Find last selected index
            var lastSelectedIndex = -1;
            inputs.forEach(function (inp, idx) {
                if (inp.files && inp.files.length > 0) {
                    lastSelectedIndex = idx;
                }
            });

            // Remove trailing empty inputs, keep at most one empty after last selected (if we are below max)
            var total = inputs.length;
            var allowedTrailing = total < max ? 1 : 0;
            for (var i = inputs.length - 1; i > lastSelectedIndex + allowedTrailing; i--) {
                var candidate = inputs[i];
                if (!candidate.files || candidate.files.length === 0) {
                    // If there is a label referencing this id, avoid duplicate for-ids later
                    var lbl = candidate.id ? parent.querySelector('label[for="' + candidate.id + '"]') : null;
                    if (lbl) {
                        lbl.removeAttribute('for');
                    }
                    candidate.parentNode && candidate.parentNode.removeChild(candidate);
                } else {
                    // stop once we hit a non-empty
                    break;
                }
            }

            // Recompute after removals
            inputs = [].slice.call(parent.querySelectorAll('input.upload-field[type="file"]'));
            if (!inputs.length) {
                return;
            }

            var last = inputs[inputs.length - 1];
            var countNow = inputs.length;

            // If last has a file and we are below max, append a fresh empty input
            if (last && last.files && last.files.length > 0 && countNow < max) {
                var clone = last.cloneNode(true);
                try {
                    clone.value = '';
                } catch (e) { /* some browsers disallow programmatic clear; ignore */
                }
                // Ensure no duplicate id on clone
                if (clone.id) {
                    clone.removeAttribute('id');
                }
                // Insert after the last
                last.insertAdjacentElement('afterend', clone);
            }
        }

        // Initial normalization for each group (by parent wrapper)
        var parents = [];
        initialInputs.forEach(function (inp) {
            var p = getParent(inp);
            if (parents.indexOf(p) === -1) {
                parents.push(p);
            }
        });
        parents.forEach(ensureSlots);

        // Event delegation: on any change in a file input with class upload-field
        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || !t.matches) {
                return;
            }
            if (t.matches('input.upload-field[type="file"]')) {
                ensureSlots(getParent(t));
            }
        });
    }

    checkUploadMaxItem();

    var toastEl = document.getElementById('already-participant-toast');
    if (isAccessible(toastEl)) {
        var toast = bootstrap.Toast.getOrCreateInstance(toastEl)
        toast.show();
    }
});