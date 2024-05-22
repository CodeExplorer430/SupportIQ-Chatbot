$(document).ready(function () {
  $('#form-open').click(function () {
      $('#loginModal').modal('show');
  });

  $('#showSignup').click(function (e) {
      e.preventDefault();
      $('#loginModal').modal('hide');
      $('#signupModal').modal('show');
  });

  $('#showLogin').click(function (e) {
      e.preventDefault();
      $('#signupModal').modal('hide');
      $('#loginModal').modal('show');
  });
});