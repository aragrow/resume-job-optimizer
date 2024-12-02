document.addEventListener('DOMContentLoaded', function () {
    const image = document.getElementById('rotating-image');
    const button = document.getElementById('rjo_match_submit');

    // Add event listener for the button
    button.addEventListener('click', function () {
      // Add the rotate class to start animation
      image.classList.add('rotate');
    });
  });