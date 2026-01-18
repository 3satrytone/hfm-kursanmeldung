// ES6: Copy-to-Clipboard für Teilnehmer Edit Formular
// Kopiert beim Klick auf Buttons mit Klasse .copyButton den Wert des
// zugehörigen Inputs mit Klasse .linkToCopy in die Zwischenablage.

document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.copyButton');
  if (!buttons.length) return;

  const copyText = async (text) => {
    // Bevorzugt moderne Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(text);
      return true;
    }
    // Fallback für ältere Browser: temporäres Textarea verwenden
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'absolute';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
      const ok = document.execCommand('copy');
      document.body.removeChild(ta);
      return ok;
    } catch (e) {
      document.body.removeChild(ta);
      return false;
    }
  };

  const findAssociatedInput = (btn) => {
    // 1) Suche im gleichen Container nach .linkToCopy
    const container = btn.closest('.joFormWrapper, td, .form-group, div') || btn.parentElement;
    if (container) {
      const inputInContainer = container.querySelector('input.linkToCopy, .linkToCopy');
      if (inputInContainer) return inputInContainer;
    }
    // 2) Direkter vorheriger/benachbarter Knoten
    let sib = btn.previousElementSibling;
    while (sib) {
      if (sib.matches && sib.matches('input.linkToCopy, .linkToCopy')) return sib;
      sib = sib.previousElementSibling;
    }
    // 3) Globaler Fallback: erstes Element mit .linkToCopy
    return document.querySelector('input.linkToCopy, .linkToCopy');
  };

  buttons.forEach((btn) => {
    if (btn.dataset.copyBound === '1') return;
    btn.dataset.copyBound = '1';
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const input = findAssociatedInput(btn);
      if (!input) return;
      const value = (input.value !== undefined) ? input.value : (input.textContent || '').trim();
      if (!value) return;

      const originalHtml = btn.innerHTML;
      try {
        const ok = await copyText(value);
        if (ok) {
          btn.innerHTML = 'kopiert';
        } else {
          btn.innerHTML = 'Fehler';
        }
      } catch (ex) {
        btn.innerHTML = 'Fehler';
      }
      // Nach kurzer Zeit Button-Beschriftung zurücksetzen
      setTimeout(() => {
        btn.innerHTML = originalHtml;
      }, 1500);
    });
  });
});
