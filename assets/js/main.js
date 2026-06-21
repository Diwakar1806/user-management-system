const navbarHTML = `
<nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom">
  <div class="container-fluid">
    <a class="navbar-brand" href="login.html">Developer's &#129309; Sp&#128526;t | Access</a>
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
    <small>&copy; Developer's Spot. All rights reserved.</small>
  </div>
</footer>
`;

let bubbleBurstTimer;

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

function createBubbleField() {
  if (document.querySelector('.bubble-field')) return;

  const bubbleField = document.createElement('div');
  bubbleField.className = 'bubble-field';
  bubbleField.setAttribute('aria-hidden', 'true');

  const bubbles = [
    { left: 3, size: 112, duration: 25, delay: -3, drift: 54, end: -42, scale: 1 },
    { left: 9, size: 74, duration: 20, delay: -11, drift: -34, end: 46, scale: .92 },
    { left: 15, size: 148, duration: 30, delay: -18, drift: 66, end: -58, scale: 1.08 },
    { left: 22, size: 92, duration: 24, delay: -7, drift: -48, end: 38, scale: .96 },
    { left: 30, size: 130, duration: 28, delay: -15, drift: 42, end: -62, scale: 1.04 },
    { left: 38, size: 68, duration: 19, delay: -5, drift: -40, end: 36, scale: .9 },
    { left: 45, size: 156, duration: 32, delay: -23, drift: 58, end: -44, scale: 1.12 },
    { left: 53, size: 84, duration: 22, delay: -9, drift: -52, end: 48, scale: .95 },
    { left: 60, size: 118, duration: 27, delay: -17, drift: 44, end: -56, scale: 1 },
    { left: 67, size: 76, duration: 21, delay: -13, drift: -38, end: 42, scale: .9 },
    { left: 74, size: 142, duration: 31, delay: -21, drift: 62, end: -50, scale: 1.1 },
    { left: 82, size: 96, duration: 23, delay: -6, drift: -46, end: 52, scale: .98 },
    { left: 90, size: 128, duration: 29, delay: -16, drift: 50, end: -60, scale: 1.05 },
    { left: 96, size: 72, duration: 20, delay: -10, drift: -30, end: 36, scale: .88 },
    { left: 6, size: 164, duration: 34, delay: -25, drift: 72, end: -66, scale: 1.16 },
    { left: 26, size: 58, duration: 18, delay: -2, drift: -24, end: 32, scale: .86 },
    { left: 49, size: 104, duration: 26, delay: -12, drift: 36, end: -46, scale: .98 },
    { left: 70, size: 188, duration: 36, delay: -27, drift: -68, end: 72, scale: 1.2 },
    { left: 88, size: 62, duration: 19, delay: -4, drift: 28, end: -34, scale: .86 }
  ];

  bubbles.forEach(config => {
    const bubble = document.createElement('span');
    bubble.className = 'bubble';
    bubble.style.setProperty('--bubble-left', `${config.left}%`);
    bubble.style.setProperty('--bubble-size', `${config.size}px`);
    bubble.style.setProperty('--bubble-duration', `${config.duration}s`);
    bubble.style.setProperty('--bubble-delay', `${config.delay}s`);
    bubble.style.setProperty('--bubble-drift', `${config.drift}px`);
    bubble.style.setProperty('--bubble-drift-end', `${config.end}px`);
    bubble.style.setProperty('--bubble-scale', config.scale);
    bubbleField.appendChild(bubble);
  });

  document.body.prepend(bubbleField);
}

function burstBubbles() {
  const bubbleField = document.querySelector('.bubble-field');
  if (!bubbleField || bubbleField.classList.contains('is-bursting')) return;

  bubbleField.classList.add('is-bursting');
  clearTimeout(bubbleBurstTimer);

  bubbleField.querySelectorAll('.bubble').forEach(bubble => {
    const rect = bubble.getBoundingClientRect();
    bubble.style.animation = 'none';
    bubble.style.bottom = 'auto';
    bubble.style.height = `${rect.height}px`;
    bubble.style.left = `${rect.left}px`;
    bubble.style.top = `${rect.top}px`;
    bubble.style.transform = 'none';
    bubble.style.width = `${rect.width}px`;
    bubble.classList.add('bursting');
  });

  bubbleBurstTimer = setTimeout(() => {
    bubbleField.remove();
    createBubbleField();
  }, 820);
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
      burstBubbles();
      clearValidation(loginForm);
      const email = document.getElementById('login-email');
      const password = document.getElementById('login-password');
      let valid = true;
      if (!email.value.trim()) { email.classList.add('is-invalid'); valid = false; }
      if (!password.value.trim()) { password.classList.add('is-invalid'); valid = false; }
      if (!valid) return;
      // For prototype: simulate success
      // If frontend validation passes, submit the form to the PHP backend!
      loginForm.submit();
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
      burstBubbles();
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
      // If frontend validation passes, submit the form to the PHP backend!
      registerForm.submit();
      // Optionally: clear form or show success
    });
  }
}

document.addEventListener('DOMContentLoaded', function () {
  createBubbleField();
  injectLayoutComponents();
  setupPasswordToggles();
  attachFormHandlers();

  // Remove 'is-invalid' on input
  document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('is-invalid'));
  });
});
