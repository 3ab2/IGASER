// Fonction pour ajouter un nouveau bloc d'éléments dynamiques
function ajouterBloc(nomSection) {
  const conteneur = document.getElementById("conteneur_" + nomSection);
  const modele = document.getElementById("modele_" + nomSection);
  
  // Vérification que les éléments existent
  if (!conteneur || !modele) {
    console.error("Erreur: Conteneur ou modèle introuvable pour " + nomSection);
    return;
  }
  
  const nouveauBloc = modele.cloneNode(true);
  nouveauBloc.style.display = 'block';
  nouveauBloc.removeAttribute('id');
  
  // Ajouter une animation d'apparition
  nouveauBloc.style.opacity = '0';
  nouveauBloc.style.transform = 'translateY(-10px)';
  
  conteneur.appendChild(nouveauBloc);
  
  // Animation d'apparition
  requestAnimationFrame(() => {
    nouveauBloc.style.transition = 'all 0.3s ease';
    nouveauBloc.style.opacity = '1';
    nouveauBloc.style.transform = 'translateY(0)';
  });
  
  // Focus sur le premier champ du nouveau bloc
  const premierInput = nouveauBloc.querySelector('input, select');
  if (premierInput) {
    premierInput.focus();
  }
}

// Fonction pour supprimer un bloc
function supprimerBloc(bouton) {
  const bloc = bouton.closest('.bloc');
  if (bloc) {
    // Animation de disparition
    bloc.style.transition = 'all 0.3s ease';
    bloc.style.opacity = '0';
    bloc.style.transform = 'translateX(-20px)';
    
    setTimeout(() => {
      bloc.remove();
    }, 300);
  }
}

// Fonction pour afficher le formulaire
function afficherFormulaire() {
  const form = document.querySelector(".formulaires");
  const bouton = document.getElementById("btn-nouveau");
  
  if (form && bouton) {
    form.classList.add("active");
    bouton.style.display = "none";
    
    // Focus sur le premier champ
    const premierInput = form.querySelector('input');
    if (premierInput) {
      setTimeout(() => premierInput.focus(), 100);
    }
  }
}

// Fonction pour masquer le formulaire
function masquerFormulaire() {
  const form = document.querySelector(".formulaires");
  const bouton = document.getElementById("btn-nouveau");
  
  if (form && bouton) {
    form.classList.remove("active");
    bouton.style.display = "block";
    
    // Réinitialiser le formulaire
    form.reset();
    
    // Supprimer tous les blocs dynamiques
    ['employe', 'engin', 'materiel', 'fournisseur', 'commande', 'facture'].forEach(section => {
      const conteneur = document.getElementById("conteneur_" + section);
      if (conteneur) {
        conteneur.innerHTML = '';
      }
    });
  }
}

// Validation en temps réel des dates
document.addEventListener('DOMContentLoaded', function() {
  const dateDebut = document.getElementById('projet_debut');
  const dateFin = document.getElementById('projet_fin');
  
  function validerDates() {
    if (dateDebut.value && dateFin.value) {
      if (new Date(dateDebut.value) > new Date(dateFin.value)) {
        dateFin.setCustomValidity('La date de fin doit être postérieure à la date de début');
      } else {
        dateFin.setCustomValidity('');
      }
    }
  }
  
  if (dateDebut && dateFin) {
    dateDebut.addEventListener('change', validerDates);
    dateFin.addEventListener('change', validerDates);
  }
  
  // Validation du formulaire avant soumission
  const form = document.querySelector('.formulaires');
  if (form) {
    form.addEventListener('submit', function(e) {
      // Vérifier que les dates sont cohérentes
      validerDates();
      
      // Vérifier qu'au moins un employé est ajouté
      const employes = document.querySelectorAll('#conteneur_employe .bloc');
      if (employes.length === 0) {
        alert('Veuillez ajouter au moins un employé au projet.');
        e.preventDefault();
        return false;
      }
      
      // Validation des champs requis dans les blocs dynamiques
      const champsVides = form.querySelectorAll('.bloc input[required]');
      let erreur = false;
      
      champsVides.forEach(champ => {
        if (!champ.value.trim()) {
          champ.style.borderColor = '#dc3545';
          erreur = true;
        } else {
          champ.style.borderColor = '#ced4da';
        }
      });
      
      if (erreur) {
        alert('Veuillez remplir tous les champs obligatoires.');
        e.preventDefault();
        return false;
      }
    });
  }
  
  // Gestion des raccourcis clavier
  document.addEventListener('keydown', function(e) {
    // Échapper pour fermer le formulaire
    if (e.key === 'Escape') {
      const form = document.querySelector('.formulaires.active');
      if (form) {
        masquerFormulaire();
      }
    }
    
    // Ctrl+N pour nouveau projet
    if (e.ctrlKey && e.key === 'n') {
      e.preventDefault();
      const bouton = document.getElementById('btn-nouveau');
      if (bouton && bouton.style.display !== 'none') {
        afficherFormulaire();
      }
    }
  });
});

// Fonction utilitaire pour formater les nombres
function formaterNombre(input) {
  const valeur = parseFloat(input.value);
  if (!isNaN(valeur)) {
    input.value = valeur.toFixed(2);
  }
}

// Auto-complétion pour les IDs (simple suggestion)
function suggererId(prefixe) {
  const timestamp = Date.now().toString().slice(-6);
  const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
  return prefixe + timestamp + random;
}

// Ajouter des suggestions d'ID automatiques
document.addEventListener('DOMContentLoaded', function() {
  // Suggérer des IDs pour les nouveaux éléments
  document.addEventListener('click', function(e) {
    if (e.target.type === 'button' && e.target.textContent.includes('Ajouter')) {
      setTimeout(() => {
        const nouveauxInputsId = document.querySelectorAll('input[name*="_id[]"]:not([data-suggested])');
        nouveauxInputsId.forEach(input => {
          if (!input.value) {
            const type = input.name.split('_')[0];
            input.placeholder += ' (Suggestion: ' + suggererId(type.toUpperCase()) + ')';
            input.setAttribute('data-suggested', 'true');
          }
        });
      }, 100);
    }
  });
});