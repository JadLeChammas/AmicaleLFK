// Gère le clic sur les boutons de continents
document.querySelectorAll('.continent-btn').forEach(button => {
  button.addEventListener('click', () => {
    const continent = button.getAttribute('data-continent');
    fetch('get_universities_by_continent.php?continent=' + encodeURIComponent(continent))
      .then(response => response.text())
      .then(data => {
        document.getElementById('output').innerHTML = data;

        // Réactiver les événements de toggle après chargement
        enableAllToggles();
      });
  });
});

// Fonction générique pour tous les éléments cliquables (pays, ville, université)
function enableAllToggles() {
  document.querySelectorAll('.toggle-block').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const targetId = this.dataset.target;
      const target = document.getElementById(targetId);
      if (target.style.display === 'none') {
        target.style.display = 'block';
        this.textContent = '▼ ' + this.textContent.slice(2);
      } else {
        target.style.display = 'none';
        this.textContent = '▶ ' + this.textContent.slice(2);
      }
    });
  });
}
