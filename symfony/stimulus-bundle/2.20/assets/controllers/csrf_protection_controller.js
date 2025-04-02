const nameCheck = /^[-_a-zA-Z0-9]{4,22}$/;
const tokenCheck = /^[-_/+a-zA-Z0-9]{24,}$/;

export function generateCsrfToken(formElement) {
  const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

  if (!csrfField) {
    return;
  }

  let csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
  let csrfToken = csrfField.value;

  if (!csrfCookie && nameCheck.test(csrfToken)) {
    csrfField.setAttribute('data-csrf-protection-cookie-value', (csrfCookie = csrfToken));
    csrfToken = btoa(
      String.fromCharCode.apply(null, (window.crypto || window.msCrypto).getRandomValues(new Uint8Array(18))),
    );
    csrfField.defaultValue = csrfToken;
    csrfField.dispatchEvent(new Event('change', { bubbles: true }));
  }

  if (csrfCookie && tokenCheck.test(csrfToken)) {
    const cookie = `${csrfCookie}_${csrfToken}=${csrfCookie}; path=/; samesite=strict`;
    document.cookie = window.location.protocol === 'https:' ? `__Host-${cookie}; secure` : cookie;
  }
}

export function generateCsrfHeaders(formElement) {
  const headers = {};
  const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

  if (!csrfField) {
    return headers;
  }

  const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

  if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
    headers[csrfCookie] = csrfField.value;
  }

  return headers;
}

export function removeCsrfToken(formElement) {
  const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

  if (!csrfField) {
    return;
  }

  const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

  if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
    const cookie = `${csrfCookie}_${csrfField.value}=0; path=/; samesite=strict; max-age=0`;

    document.cookie = window.location.protocol === 'https:' ? `__Host-${cookie}; secure` : cookie;
  }
}

// Generate and double-submit a CSRF token in a form field and a cookie, as defined by Symfony's SameOriginCsrfTokenManager
document.addEventListener(
  'submit',
  (event) => {
    generateCsrfToken(event.target);
  },
  true,
);

// When @hotwired/turbo handles form submissions, send the CSRF token in a header in addition to a cookie
// The `framework.csrf_protection.check_header` config option needs to be enabled for the header to be checked
document.addEventListener('turbo:submit-start', (event) => {
  const h = generateCsrfHeaders(event.detail.formSubmission.formElement);
  // eslint-disable-next-line array-callback-return
  Object.keys(h).map((k) => {
    // eslint-disable-next-line no-param-reassign
    event.detail.formSubmission.fetchRequest.headers[k] = h[k];
  });
});

// When @hotwired/turbo handles form submissions, remove the CSRF cookie once a form has been submitted
document.addEventListener('turbo:submit-end', (event) => {
  removeCsrfToken(event.detail.formSubmission.formElement);
});

/* stimulusFetch: 'lazy' */
export default 'csrf-protection-controller';
