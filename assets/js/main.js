const navbarHTML = `
<nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom">
  <div class="container-fluid">
    <a class="navbar-brand" href="login.html">Developer's 🤝 Sp😎t| Access</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="login.html">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.html">Register</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
`;

const footerHTML = `
<footer class="footer bg-dark-custom text-center py-3 mt-5">
  <div class="container">
    <small>&copy; 2026 Diwakar.Dev. All rights reserved.</small>
  </div>
</footer>
`;

function injectLayoutComponents() {
  const navbarPlaceholder = document.getElementById('navbar-placeholder');
  const footerPlaceholder = document.getElementById('footer-placeholder');

  if (navbarPlaceholder) {
    navbarPlaceholder.innerHTML = navbarHTML;
  }

  if (footerPlaceholder) {
    footerPlaceholder.innerHTML = footerHTML;
  }
}

function setupPasswordToggles() {
  document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', () => {
      const targetId = icon.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
      input.focus();
    });
  });
}

function clearValidation(form) {
  form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function attachFormHandlers() {
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      clearValidation(loginForm);
      const email = document.getElementById('login-email');
      const password = document.getElementById('login-password');
      let valid = true;
      if (!email.value.trim()) { email.classList.add('is-invalid'); valid = false; }
      if (!password.value.trim()) { password.classList.add('is-invalid'); valid = false; }
      if (!valid) return;
      // For prototype: simulate success
      console.log('Login submitted', { email: email.value });
      // Optionally: show a success toast or redirect
    });
  }

  if (registerForm) {
    const takenEmails = ['admin@test.com', 'user@domain.com'];
    const emailInput = document.getElementById('register-email');
    const pwd = document.getElementById('register-password');
    const pwdConfirm = document.getElementById('register-password-confirm');
    const pwdMatchMsg = document.getElementById('password-match-msg');
    const emailTakenMsg = document.getElementById('email-taken-msg');

    emailInput.addEventListener('blur', () => {
      const v = emailInput.value.trim().toLowerCase();
      if (!v) { emailTakenMsg.classList.add('d-none'); return; }
      if (takenEmails.includes(v)) {
        emailTakenMsg.classList.remove('d-none');
        emailInput.classList.add('is-invalid');
      } else {
        emailTakenMsg.classList.add('d-none');
        emailInput.classList.remove('is-invalid');
      }
    });

    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();
      clearValidation(registerForm);
      let valid = true;
      const name = document.getElementById('register-name');
      if (!name.value.trim()) { name.classList.add('is-invalid'); valid = false; }
      if (!emailInput.value.trim()) { emailInput.classList.add('is-invalid'); valid = false; }
      if (!pwd.value.trim()) { pwd.classList.add('is-invalid'); valid = false; }
      if (!pwdConfirm.value.trim()) { pwdConfirm.classList.add('is-invalid'); valid = false; }

      // Password match check
      if (pwd.value && pwdConfirm.value && pwd.value !== pwdConfirm.value) {
        if (pwdMatchMsg) pwdMatchMsg.classList.remove('d-none');
        pwdConfirm.classList.add('is-invalid');
        valid = false;
      } else if (pwdMatchMsg) {
        pwdMatchMsg.classList.add('d-none');
      }

      // Email taken check
      if (takenEmails.includes(emailInput.value.trim().toLowerCase())) {
        emailTakenMsg.classList.remove('d-none');
        emailInput.classList.add('is-invalid');
        valid = false;
      }

      if (!valid) return;

      // Simulate AJAX registration
      console.log('Register submitted', { name: name.value, email: emailInput.value });
      // Optionally: clear form or show success
    });
  }
}

document.addEventListener('DOMContentLoaded', function () {
  injectLayoutComponents();
  setupPasswordToggles();
  attachFormHandlers();

  // Remove 'is-invalid' on input
  document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('is-invalid'));
  });
});
