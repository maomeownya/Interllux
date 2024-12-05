function toggleForms() {
  const signUpForm = document.getElementById('signUpForm');
  const signInForm = document.getElementById('signInForm');

  if (signUpForm.style.display === 'none') {
    signUpForm.style.display = 'block';
    signInForm.style.display = 'none';

    // Clear the input values in the sign-in form
    document.getElementById('signInEmail').value = '';
    document.getElementById('signInPassword').value = '';
  } else {
    signUpForm.style.display = 'none';
    signInForm.style.display = 'block';

    // Clear the input values in the sign-up form
    document.getElementById('signUpFirstName').value = '';
    document.getElementById('signUpLastName').value = '';
    document.getElementById('signUpEmail').value = '';
    document.getElementById('signUpPassword').value = '';
    document.getElementById('signUpConfirmPassword').value = '';
  }
}

//This is a property of PLSP-CCST BSIT-3B SY 2024-2025