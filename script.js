// Permet d'empêcher l'animation de se déclencher au rechargement de la page
document.addEventListener('DOMContentLoaded', function() {
  const links = document.querySelectorAll('.linkanimation'); 
  links.forEach(link => {
    link.addEventListener('mouseenter', function() {
      link.classList.add('animation-ready');
    }, { once: true });
  });
}); 

document.addEventListener('DOMContentLoaded', function () {
  const links = document.querySelectorAll('.linkanimation');

  links.forEach(link => {
    // Ajoute un événement au survol
    link.addEventListener('mouseenter', function () {
      link.classList.add('hovering'); // Ajoute la classe pour l'animation hover
      link.classList.remove('not-hovering'); // Supprime la classe reverse si elle existe
    });

    // Ajoute un événement lorsque la souris quitte le lien
    link.addEventListener('mouseleave', function () {
      // Attends la fin de l'animation hover avant de lancer l'animation reverse
      link.addEventListener('animationend', function () {
        if (!link.matches(':hover')) { // Vérifie que la souris n'est plus sur le lien
          link.classList.remove('hovering'); // Supprime la classe hover
          link.classList.add('not-hovering'); // Ajoute la classe pour l'animation reverse
        }
      }, { once: true }); // L'événement est déclenché une seule fois
    });
  });
});

// Curseur personnalisé 
var cursor = document.getElementById("cursor");
document.body.addEventListener("mousemove", function(e) {
  cursor.style.left = e.clientX + "px",
    cursor.style.top = e.clientY + "px";
});

// Grossissement du bouton au survol
document.addEventListener('DOMContentLoaded', function () {
  const button = document.querySelector('#index button'); 

  button.addEventListener('mouseenter', function () {
    button.style.transform = 'scale(1.05)'; 
    button.style.transition = 'transform 0.3s ease'; 
  });

  button.addEventListener('mouseleave', function () {
    button.style.transform = 'scale(1)';
  });
});

// Scroll vers le deuxième div au clic sur l'icône
document.addEventListener('DOMContentLoaded', function () {
  const scrollIcon = document.getElementById('scroll-icon'); // Sélectionne l'icône
  const secondDiv = document.getElementById('ancre-scroll'); // Sélectionne le deuxième div

  // Ajoute un événement au clic sur l'icône
  scrollIcon.addEventListener('click', function () {
    secondDiv.scrollIntoView({ behavior: 'smooth' }); // Défilement fluide vers le deuxième div
  });
});


// Menu dropdown leaderboard





