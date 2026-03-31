<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Formulário com reCAPTCHA</title>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    
    <div class="g-recaptcha" data-sitekey="6LdtRxssAAAAAIwwZka8vUpdB6wUne6PvYZn79_7"></div>

  <script>
    document.getElementById('myForm').addEventListener('submit', function(e){
      var response = grecaptcha.getResponse();
      if (response.length === 0) {
        e.preventDefault();
        alert('Por favor, marque o reCAPTCHA antes de enviar o formulário.');
      }
    });
  </script>

</body>
</html>