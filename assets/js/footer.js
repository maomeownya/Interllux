 
    fetch('../../user/component/footer.php')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(data => {
        document.getElementById('footer').innerHTML = data;

      })
      .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
      });
      //This is a property of PLSP-CCST BSIT-3B SY 2024-2025