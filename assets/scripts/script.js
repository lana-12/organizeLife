window.onload= () => {




function Supp(link) {
    console.log('j ai cliquer');
    if (confirm('Confirmez la suppression du projet ?')) {
        document.location.href = link;
    }
}

}