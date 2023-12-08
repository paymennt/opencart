$(document).ready(function () {
  var payButton = document.getElementById("button-confirm");
  var public_key = document.getElementById("pkey").value;
  var errorMessage = document.querySelector(".error-message");
  var token = document.getElementById("pptoken");
   var mode = document.getElementById("pmode").value;
  //console.log('I am public key'+public_key);

  payButton.disabled = true;
  PaymenntJS.init({
    publicKey: public_key,
    mode:
      mode == 1
        ? PaymenntJS.modes.LIVE
        : mode == 2
        ? PaymenntJS.modes.STAGING
        : PaymenntJS.modes.TEST,
    onTokenized: function (data) {
      token.value = data.token;
      if (data.token != "") {
        $.ajax({
          url: "index.php?route=extension/paymennt/payment/paymennt|charge",
          type: "post",
          data: $("#form-credit-card").serialize(),
          dataType: "json",
          contentType: "application/x-www-form-urlencoded",
          cache: false,
          processData: false,
          beforeSend: function () {
            $("#button-confirm").button("loading");
          },
          complete: function () {
            $("#button-confirm").button("reset");
          },
          success: function (json) {
            $(".alert-dismissible").remove();
            //$('#form-credit-card').find('.is-invalid').removeClass('is-invalid');
            //$('#form-credit-card').find('.invalid-feedback').removeClass('d-block');

            if (json["redirect"]) {
              location = json["redirect"];
            }

            if (json["error"]) {
              if (json["error"]["warning"]) {
                $("#alert").prepend(
                  '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' +
                    json["error"]["warning"] +
                    ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                );
              }

              for (key in json["error"]) {
                $("#input-" + key.replaceAll("_", "-"))
                  .addClass("is-invalid")
                  .find(
                    ".form-control, .form-select, .form-check-input, .form-check-label"
                  )
                  .addClass("is-invalid");
                $("#error-" + key.replaceAll("_", "-"))
                  .html(json["error"][key])
                  .addClass("d-block");
              }
            }
          },
          error: function (xhr, ajaxOptions, thrownError) {
            $("#alert").prepend(
              '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> ' +
                xhr.responseText +
                ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
            );
          },
        });
      }
    },
    onTokenizationFailed: function (data) {
      errorMessage.innerText = data.error;
    },

    onValidationUpdate: function (data) {
      payButton.disabled = !data.valid;
      if (!data.valid) {
        var message = null;
        for (var i = 0; i < data.validationErrors.length; ++i) {
          var fieldValidation = data.validationErrors[i];
          if (!message || fieldValidation.field === data.lastActiveField) {
            message = fieldValidation.message;
          }
        }
        if (!message) {
          message = data.error ? data.error : "Invalid card details";
        }
        errorMessage.innerText = message;
      } else {
        errorMessage.innerText = "";
      }
    },
  });

  payButton.addEventListener("click", function (event) {
    event.preventDefault();
    PaymenntJS.submitPayment();
  });
});