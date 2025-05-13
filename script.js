// Permet d'empêcher l'animation de se déclencher au rechargement de la page
document.addEventListener('DOMContentLoaded', function() {
  const links = document.querySelectorAll('.linkanimation'); 
  links.forEach(link => {
    link.addEventListener('mouseenter', function() {
      link.classList.add('animation-ready');
    }, { once: true });
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