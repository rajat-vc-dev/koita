
// =========================
// Scroll to form top
// =========================
function scrollToFormTop() {
  var top = document.getElementById('formTop');
  if (!top) return;

  window.scrollTo({
    top: top.offsetTop - 20,
    behavior: 'smooth'
  });
}

// =========================
// Invite Gate
// =========================
document.getElementById('gateBtn').addEventListener('click', function () {
  if (!window.validateForm(document.getElementById('gateForm'))) return;

  document.getElementById('gate').style.display = 'none';
  document.getElementById('appWrap').style.display = 'block';

  setTimeout(scrollToFormTop, 50);
});

// =========================
// File upload label
// =========================
document.querySelectorAll('.file-drop input[type=file]').forEach(function (inp) {
  inp.addEventListener('change', function () {
    var lbl = inp.parentElement.querySelector('.fl');

    if (inp.files.length) {
      lbl.textContent = inp.files.length > 1
        ? inp.files.length + ' files selected'
        : inp.files[0].name;
    }
  });
});

// =========================
// Word Counter
// =========================
document.querySelectorAll('textarea[data-words]').forEach(function (t) {

  var max = +t.dataset.words;
  var hint = t.previousElementSibling;

  if (!hint || !hint.classList.contains('hint')) hint = null;

  var base = hint ? hint.textContent : '';

  t.addEventListener('input', function () {

    var n = t.value.trim()
      ? t.value.trim().split(/\s+/).length
      : 0;

    if (hint) {
      hint.textContent = base + " (" + n + "/" + max + ")";
    }

  });

});

// =========================
// Adoption radio
// =========================
document.getElementById('adoptGroup').addEventListener('change', function () {

  var v = document.querySelector('input[name=adopt]:checked');
  var uc = document.getElementById('userCount');

  uc.style.display = (v && v.value !== 'dev')
    ? 'block'
    : 'none';

});

// =========================
// Multi Step Form
// =========================
var cur = 0;

var steps = document.querySelectorAll('.fstep');
var stps = document.querySelectorAll('.stp');

var prevB = document.getElementById('prevBtn');
var nextB = document.getElementById('nextBtn');

function showStep(i) {

  steps.forEach(function (s, k) {
    s.classList.toggle('active', k === i);
  });

  stps.forEach(function (s, k) {
    s.classList.toggle('active', k === i);
    s.classList.toggle('done', k < i);
  });

  prevB.style.visibility = i === 0 ? 'hidden' : 'visible';

  nextB.innerHTML =
    i === steps.length - 1
      ? 'Submit Application <span class="arw">&rarr;</span>'
      : 'Continue <span class="arw">&rarr;</span>';

  cur = i;

  document.getElementById('stepper').scrollLeft = i * 90;

  setTimeout(scrollToFormTop, 50);
}

function validateStep(i) {

  var scope = steps[i];
  var ok = window.validateForm(scope);

  if (i === 2) {

    var r = scope.querySelector('input[name=adopt]:checked');
    var err = document.getElementById('adoptErr');

    if (!r) {
      ok = false;
      err.style.display = 'block';
    } else {
      err.style.display = 'none';
    }

  }

  return ok;

}

prevB.addEventListener('click', function () {

  if (cur > 0) {
    showStep(cur - 1);
  }

});

nextB.addEventListener('click', function () {

  if (!validateStep(cur)) return;

  if (cur < steps.length - 1) {

    showStep(cur + 1);

  } else {

    document.querySelector('#appWrap .form-body').style.display = 'none';
    document.getElementById('stepper').style.display = 'none';
    document.getElementById('appSuccess').classList.add('show');

    setTimeout(scrollToFormTop, 50);

  }

});
