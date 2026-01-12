// show students passiv mesage
document.addEventListener('DOMContentLoaded', function () {

    console.log('aa');

    const collapseElementList = document.querySelectorAll('.collapse')
    const collapseList = [...collapseElementList].map(collapseEl => new bootstrap.Collapse(collapseEl))
    console.log('aa');
});
