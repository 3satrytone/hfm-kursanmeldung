var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    if(popoverTriggerEl.className.includes('f3-form-error') === true){
        return new bootstrap.Popover(popoverTriggerEl)
    }else{
        popoverTriggerEl.removeAttribute('data-bs-toggle');
        popoverTriggerEl.removeAttribute('data-bs-content');
        popoverTriggerEl.removeAttribute('data-bs-placement');
    }
})

