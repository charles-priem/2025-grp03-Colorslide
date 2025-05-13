document.addEventListener('DOMContentLoaded', function() {
  const links = document.querySelectorAll('.linkanimation'); // Sélectionne tous les éléments avec la classe "linkanimation"
  links.forEach(link => {
    link.addEventListener('mouseenter', function() {
      link.classList.add('animation-ready');
    }, { once: true }); // L'événement ne se déclenche qu'une fois par élément
  });
});