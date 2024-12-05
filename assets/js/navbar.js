fetch('../../user/component/navbar.php')
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.text();
  })
  .then(data => {
    // Assumes you have an element with id="navbar" in your main HTML
    document.getElementById('navbar').innerHTML = data;
  })
  .catch(error => {
    console.error('There has been a problem with your fetch operation:', error);
  });
//This is a property of PLSP-CCST BSIT-3B SY 2024-2025