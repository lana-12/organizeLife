window.onload= () => {

    function Supp(link) {
        console.log('j ai cliquer');
        if (confirm('Confirmez la suppression du projet ?')) {
            document.location.href = link;
        }
    }

    
    //Request for registrartion
    const form = document.getElementById('registrationForm');
    const errorContainer = document.createElement('div'); 
    errorContainer.className = "alert alert-danger d-none"; 
    form.prepend(errorContainer); 

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        errorContainer.classList.add('d-none'); 
        errorContainer.innerHTML = ""; 

        const formData = new FormData(form);
        const data = {
            firstname: formData.get('firstname'),
            lastname: formData.get('lastname'),
            email: formData.get('email'),
            password: formData.get('password'),
            agreeTerms: formData.get('agreeTerms') === 'on'
        };

        fetch(form.action, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('staticBackdrop'));
                    modal.hide();
                    window.location.href = "/";
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError("Une erreur s'est produite, veuillez réessayer.");
                console.error('Erreur AJAX : ', error);
            });

        });
        
        function showError(message) {
            errorContainer.innerHTML = message;
            errorContainer.classList.remove('d-none'); 
        }


}