function validateForm() {
  let nom = document.getElementById("nom").value.trim();
  let prenom = document.getElementById("prenom").value.trim();

  // Validation du nom
  if (nom === "") {
    showMessage("Le nom est obligatoire", "error");
    return false;
  }

  if (nom.length < 2) {
    showMessage("Le nom doit contenir au moins 2 caractères", "error");
    return false;
  }

  if (!/^[a-zA-ZÀ-ÿ\s-]+$/.test(nom)) {
    showMessage("Le nom ne doit contenir que des lettres", "error");
    return false;
  }

  // Validation du prénom
  if (prenom === "") {
    showMessage("Le prénom est obligatoire", "error");
    return false;
  }

  if (prenom.length < 2) {
    showMessage("Le prénom doit contenir au moins 2 caractères", "error");
    return false;
  }

  if (!/^[a-zA-ZÀ-ÿ\s-]+$/.test(prenom)) {
    showMessage("Le prénom ne doit contenir que des lettres", "error");
    return false;
  }

  return true;
}

function showMessage(message, type) {
  // Supprimer les messages existants
  const existingMessages = document.querySelectorAll(".message");
  existingMessages.forEach((msg) => msg.remove());

  // Créer le nouveau message
  const messageDiv = document.createElement("div");
  messageDiv.className = `message ${type}`;
  messageDiv.textContent = message;

  // Insérer le message avant le formulaire
  const form = document.getElementById("studentForm");
  form.parentNode.insertBefore(messageDiv, form);

  // Faire défiler vers le message
  messageDiv.scrollIntoView({ behavior: "smooth", block: "center" });

  // Supprimer le message après 5 secondes
  setTimeout(() => {
    messageDiv.remove();
  }, 5000);
}

// Animation pour les champs du formulaire
document.addEventListener("DOMContentLoaded", function () {
  const inputs = document.querySelectorAll("input, select");

  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.style.transform = "scale(1.02)";
    });

    input.addEventListener("blur", function () {
      this.parentElement.style.transform = "scale(1)";
    });
  });

  // Animation pour les lignes du tableau
  const tableRows = document.querySelectorAll("tbody tr");
  tableRows.forEach((row, index) => {
    row.style.opacity = "0";
    row.style.transform = "translateY(20px)";

    setTimeout(() => {
      row.style.transition = "all 0.3s ease";
      row.style.opacity = "1";
      row.style.transform = "translateY(0)";
    }, index * 100);
  });
});

// Confirmation avant suppression avec animation
document.querySelectorAll(".btn-delete").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?")) {
      e.preventDefault();
    }
  });
});
